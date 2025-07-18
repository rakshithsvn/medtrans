<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Doctor;
use App\Models\Ambulance;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\Exports\CustomExport;
use App\Imports\CalendarImport;
use App\Imports\IncentiveImport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;

class EmployeeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $employeeArray = Employee::pluck('name', 'id');
        view()->share('employeeArray', $employeeArray);

    }

    // Display a listing of the employees
    public function index(Request $request, $type=null)
    {
        $title = 'Employees Information';

        $type = strtoupper($type);
       
        $employees = Employee::where('type', $type);

        $employees = $employees->paginate(25);

        return view('back.employees', compact('employees', 'request', 'title', 'type'));
    }

    // Show the form for creating a new employee
    public function create()
    {
        return view('employees.create'); // Display the form for creating a new employee
    }

    // Store a newly created employee in the database
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            //'id' => 'required|integer|unique:employees,id', // Ensure id is unique
            'name' => 'required|string|max:255',
            'employee_id'  => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:15',
            'designation' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255'
        ]);
		//dd($validated);
        $existingUserCount = Employee::where('employee_id', $request->input('employee_id'))->count();

        if ($existingUserCount > 0) {
            return redirect()->back()->with('error', 'Employee ID already exists');
        }

        $validated['user_id'] = auth()->id();

        // Create a new employee record
        Employee::create($validated);

        $loginRequest = new Request([
            'name' => $request->input('name'),
            'username' => $request->input('employee_id'),
            'email' => $request->input('email'),
            'register_type' => 'EMAIL',
            'register_by' => $request->input('type'),
            'password' => $request->input('password'),
            'password_confirmation' => $request->input('password'),
            // 'password_confirmation' => $request->input('password_confirmation'),
        ]);

        $authController = new AuthController();
        $response = $authController->postRegistration($loginRequest);

        if ($response == true) {
            return redirect()->back()->with('success', 'Employee registered successfully.');
        } else {
            return redirect()->back()->with('error', 'Already registered. Please Login');
        }

        // Redirect to the employees list with a success message
        return redirect()->back()->with('success', 'Employee created successfully');
    }

    // Show the form for editing a specific employee
    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee')); // Display the edit form with employee data
    }

    // Update the specified employee in the database
    public function update(Request $request, Employee $employee)
    {
        // Validate the updated data
        $validated = $request->validate([
            // 'id' => 'required|integer|unique:employees,id,' . $employee->id, // Allow unique for the employee being updated
            'name' => 'required|string|max:255',
            'employee_id'  => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:15',
            'designation' => 'nullable|string|max:255',
        ]);
//dd($validated);
	    $employee->update($validated);

        // return response()->json(['success' => 'Employee updated successfully!']);

        return redirect()->back()->with('success', 'Employee updated successfully');
    }

    // Remove the specified employee from the database
    public function destroy(Employee $employee)
    {
        // Delete the employee record
        $employee->delete();

        // Redirect to the employees list with a success message
        return redirect()->back()->with('success', 'Employee deleted successfully');
    }

	
    // Attendance

	public function attendance(Request $request)
    {
        $title = 'Attendance';
		$user = Auth::user();
		$inputMonth = $request->get('month', Carbon::now()->month);
        $inputYear = $request->get('year', Carbon::now()->year);
		  if (in_array(Auth::user()->register_by, ['ADMIN', 'COORDINATOR'])) {
				$employee = Employee::find($request->input('employee_id'));
		  } else {			  
				$employee = Employee::where('employee_id', $user->username)->first();
		  }
		$attendance = DB::table('employee_attendance')->where('employee_id', @$employee->id)->where('date', now()->format('Y-m-d'))->orderByDesc('id')->first();
		
		$attendances = DB::table('employee_attendance')->where('employee_id', @$employee->id)
			->whereMonth('created_at', $inputMonth)
    		->whereYear('created_at', $inputYear)
			->get();
        //$employee = Employee::all();
		//dd($attendance);
        return view('back.employee-attendance', compact('employee', 'attendance', 'attendances', 'user', 'title', 'inputMonth', 'inputYear'));
    }
	
	public function postAttendance(Request $request)
	{
		$attendance = DB::table('employee_attendance')->where('employee_id', $request->employee_id)->where('date', now()->format('Y-m-d'))->whereNull('logout_time')->orderByDesc('id')->first();
		if ($attendance) {
			$requestData['logout_time'] = now()->format('H:i:s');
            $requestData['latitude2'] = $request->latitude2;
			$requestData['longitude2'] = $request->longitude2;
			$requestData['updated_at'] = Carbon::now();
				DB::table('employee_attendance')
			->where('id', $attendance->id)->update($requestData);
			$message = "Logged out successfully";
		}
		else {
			$requestData['employee_id'] = $request->employee_id;
			$requestData['date'] = now()->format('Y-m-d');
			$requestData['login_time'] = now()->format('H:i:s');
			$requestData['latitude1'] = $request->latitude1;
			$requestData['longitude1'] = $request->longitude1;

			$requestData['user_id'] = auth()->id();
			$requestData['created_at'] = Carbon::now();
			$requestData['updated_at'] = Carbon::now();

			DB::table('employee_attendance')->insert($requestData);
			$message = "Logged in successfully";
		}		

		return redirect()->route('dashboard')->with('success', $message);
	}

    // Calendar
	
	public function getCalendar(Request $request)
    {
        $title = 'Calendar';
		$user = Auth::user();
		  if (in_array(Auth::user()->register_by, ['ADMIN', 'COORDINATOR'])) {
				$employee = Employee::find($request->input('employee_id'));
				$events = DB::table('employee_calendar')
					->join('employees', 'employee_calendar.employee_id', '=', 'employees.id')
					->select('employee_calendar.*', 'employees.name as employee_name')
					->get();
		  } else {			  
				$employee = Employee::where('employee_id', $user->username)->first();
			  	$events = DB::table('employee_calendar')->where('employee_id', @$employee->id)->get();
		  }
		
        //$employee = Employee::all();
		//dd($events);
        return view('back.employee-calendar', compact('employee', 'events', 'user', 'title'));
    }
	
	public function importCalendar(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx',
        ]);

        $user = Employee::where('employee_id', Auth::user()->username)->first();

        Excel::import(new CalendarImport(@$user->id), $request->file('file'));

        return redirect()->back()->with('success', 'Calendar events imported successfully');
    }

    public function calendarStore(Request $request)
    {
        $requestData = $request->except('_token');

        $requestData['user_id'] = auth()->id();
        $requestData['created_at'] = Carbon::now();
        $requestData['updated_at'] = Carbon::now();

        DB::table('employee_calendar')->insert($requestData);

        return redirect()->back()->with('success', 'Task created successfully');
    }

    public function calendarUpdate(Request $request, $id)
    {
        $requestData = $request->except('_token');

        $requestData['user_id'] = auth()->id();
        $requestData['updated_at'] = Carbon::now();

        DB::table('employee_calendar')->where('id', $id)->update($requestData);

        return redirect()->back()->with('success', 'Task updated successfully');
    }

}
