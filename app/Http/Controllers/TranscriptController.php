<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subject;
use App\Models\ExamCategory;
use App\Models\Gradebook;
use App\Models\Grade;
use App\Models\Classes;

class TranscriptController extends Controller
{
    private function gradeFor($avg, $grades)
    {
        if ($avg === null) return null;
        foreach ($grades as $g) {
            if ($avg >= $g->mark_from && $avg <= $g->mark_upto) return $g;
        }
        return null;
    }

    /* build the full transcript dataset for a student (must be same school) */
    private function build($student_id)
    {
        $schoolId = auth()->user()->school_id;
        $student = User::where('id', $student_id)->where('school_id', $schoolId)->firstOrFail();
        $sd = (new CommonController)->get_student_details_by_id($student_id);
        $active_session = get_school_settings($schoolId)->value('running_session');

        $subjects = Subject::where(['class_id' => $sd['class_id'], 'school_id' => $schoolId])->get();
        $exam_categories = ExamCategory::where('school_id', $schoolId)->get();
        $grades = Grade::where('school_id', $schoolId)->orderByDesc('mark_from')->get();

        $marksMap = [];
        foreach (Gradebook::where('student_id', $student_id)->where('school_id', $schoolId)->where('session_id', $active_session)->get() as $gb) {
            $marksMap[$gb->exam_category_id] = json_decode($gb->marks, true) ?: [];
        }

        $rows = [];
        $subjAverages = [];
        $subjPoints = [];
        $missing = 0;
        foreach ($subjects as $subject) {
            $vals = [];
            $rowMissing = false;
            foreach ($exam_categories as $ec) {
                $m = $marksMap[$ec->id][$subject->id] ?? null;
                if (is_numeric($m)) $vals[$ec->id] = (float) $m;
                else $rowMissing = true;
            }
            if ($rowMissing || count($vals) === 0) $missing++;
            $avg = count($vals) ? round(array_sum($vals) / count($vals), 1) : null;
            $grade = $this->gradeFor($avg, $grades);
            if ($avg !== null) {
                $subjAverages[] = $avg;
                if ($grade) $subjPoints[] = (float) $grade->grade_point;
            }
            $rows[] = ['subject' => $subject, 'vals' => $vals, 'avg' => $avg, 'grade' => $grade];
        }

        $overallAvg = count($subjAverages) ? round(array_sum($subjAverages) / count($subjAverages), 1) : null;
        $gpa = count($subjPoints) ? round(array_sum($subjPoints) / count($subjPoints), 2) : null;
        $overallGrade = $this->gradeFor($overallAvg, $grades);

        $school = \DB::table('schools')->where('id', $schoolId)->first();
        $class = Classes::find($sd['class_id']);
        $sessionTitle = \DB::table('sessions')->where('id', $active_session)->value('session_title');

        return compact('student', 'class', 'subjects', 'exam_categories', 'rows', 'overallAvg', 'gpa', 'overallGrade', 'missing', 'school', 'sessionTitle');
    }

    private function render($data, $pdf = false)
    {
        if ($pdf) {
            $logoFile = get_settings('dark_logo');
            $logoPath = $logoFile ? public_path('assets/uploads/logo/' . $logoFile) : null;
            if ($logoPath && !file_exists($logoPath)) $logoPath = null;
            $data['logoPath'] = $logoPath;
            $p = \PDF::loadView('transcript.pdf', $data);
            $p->setPaper('a4');
            return $p->download('Transcript-' . str_replace(' ', '-', $data['student']->name) . '.pdf');
        }
        return view('transcript.view', $data);
    }

    /* ---- student (own) ---- */
    public function studentTranscript()
    {
        return $this->render($this->build(auth()->user()->id));
    }

    public function studentTranscriptPdf()
    {
        return $this->render($this->build(auth()->user()->id), true);
    }

    /* ---- admin (any student) ---- */
    public function adminTranscript($student_id)
    {
        return $this->render($this->build($student_id));
    }

    public function adminTranscriptPdf($student_id)
    {
        return $this->render($this->build($student_id), true);
    }
}
