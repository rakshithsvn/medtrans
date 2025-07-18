<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Employee;

class PersonImport implements ToModel, WithStartRow
{
    private $userId;

    public function __construct($userId, $type)
    {
        $this->userId = $userId;
        $this->type = $type;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
		if (empty($row[1])) {
			return null; 
		}
		if($this->type == 'Corporate') {
			$employee = Employee::where('employee_id', @$row[5])->first();
    		$employeeId = $employee ? $employee->id : $this->userId;
		 DB::table('employee_persons')->insert([
				//'sl_no'               => $row[0] ?? null, 
				'type'                => $this->type ?? null, 
				'name'                => $row[1] ?? null, 
				'point_of_contact'    => $row[2] ?? null, 
				'phone_no'            => $row[3] ?? null, 
				'email_id'            => $row[4] ?? null, 
				'employee_id'        => $employeeId,
				'user_id'             => $this->userId,
			]);
		} else {
			$employee = Employee::where('employee_id', $row[7])->first();
    		$employeeId = $employee ? $employee->id : $this->userId;
		DB::table('employee_persons')->insert([
				//'sl_no'               => $row[0] ?? null, 
				'type'                => $this->type ?? null, 
				'name'                => $row[1] ?? null, 
				'speciality'          => $row[2] ?? null, 
				'phone_no'            => $row[3] ?? null, 
				'hospital_clinic'     => $row[4] ?? null, 
				'area'                => $row[5] ?? null, 
				'district'            => $row[6] ?? null, 
				'employee_id'        => $employeeId,
				'user_id'             => $this->userId,
			]);
		}

        return null;
    }
}
