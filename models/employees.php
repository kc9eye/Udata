<?php
/* This file is part of UData.
 * Copyright (C) 2018 Paul W. Lane <kc9eye@outlook.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */
/**
 * Employees Class Model
 * 
 * @package UData\Models\Database\Postgres
 * @link https://kc9eye.github.io/udata/UData_Database_Structure.html
 * @author Paul W. Lane
 * @license GPLv2
 */
class Employees extends Profiles {

    public function __construct (PDO $dbh) {
        parent::__construct($dbh);
    }

    /**
     * Handles creating a new profile for an employee
     * @param Array $data The data array in the form:
     * `['uid'=>string,'start_date'=>string,'status'=>string,'first'=>string,'middle'=>string,
     * 'last=>string,'other'=>string,'email'=>string,'alt_email'=>string,'address'=>string,
     * 'address_other'=>string,'city'=>string,'state_prov'=>string,'postal_code'=>string,
     * 'home_phone'=>string,'cell_phone'=>string,'alt_phone'=>string,'e_contact_name'=>string,
     * 'e_contact_number'=>string,'e_contact_relation'=>string]`
     * @return Boolean True on success, false otherwise
     */
    public function addNewEmployee (Array $data) {
        $sql = 'INSERT INTO employees VALUES (:id, :status, :pid, :start_date::date, :end_date::date, :photo_id, now(), :uid)';
        try {
            if (!$this->createNewProfile($data)) throw new Exception("Create profile failed");
            $insert = [
                ':id'=>uniqid(),
                ':status'=>$data['status'],
                ':pid'=>$this->pid,
                ':start_date'=>$data['start_date'],
                ':end_date'=>null,
                ':photo_id'=>$data['fid'],
                ':uid'=>$data['uid']
            ];
            $pntr = $this->dbh->prepare($sql);
            if (!$pntr->execute($insert)) throw new Exception("Insert failed: {$sql}");
            return true;
        }
        catch (Exception $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }
    }

    /**
     * Searches the table for matching employees
     * @param String A ts_query() formated string to search for
     * @return Array Returns a multidimensional array of results on success, false on error.
     */
    public function searchEmployees ($search_string) {
        $sql = "
            SELECT
                employees.id as id,
                profiles.first||' '||profiles.middle||' '||profiles.last||' '||profiles.other as name,
                employees.start_date as start_date, employees.end_date as end_date
            FROM employees
            INNER JOIN profiles
            ON profiles.id = employees.pid
            WHERE profiles.search_index @@ to_tsquery(?)
            AND profiles.id IN (SELECT pid FROM employees)";
        try {
            $pntr = $this->dbh->prepare($sql);
            if (!$pntr->execute([$search_string])) throw new Exception("Select failed: {$sql}");
            return $pntr->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }
        catch (Exception $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }
    }

    /**
     * Handles updating a profile for an employee
     * @param Array $data The data array in the form:
     * `['eid'=>string,uid'=>string,'start_date'=>string,'status'=>string,'first'=>string,'middle'=>string,
     * 'last=>string,'other'=>string,'email'=>string,'alt_email'=>string,'address'=>string,
     * 'address_other'=>string,'city'=>string,'state_prov'=>string,'postal_code'=>string,
     * 'home_phone'=>string,'cell_phone'=>string,'alt_phone'=>string,'e_contact_name'=>string,
     * 'e_contact_number'=>string,'e_contact_relation'=>string]`
     * @return Boolean True on success, false otherwise
     */
    public function updateEmployee (Array $data) {
        if (!empty($data['end_date'])) {
            $sql = 'UPDATE employees SET start_date=:start_date::date,end_date=:end_date::date,photo_id=:fid,status=:status,_date=now(),uid=:uid WHERE id=:id';
            $insert = [
                ':start_date'=>$data['start_date'],
                ':end_date'=>$data['end_date'],
                ':fid'=>$data['fid'],
                ':status'=>$data['status'],
                ':uid'=>$data['uid'],
                ':id'=>$data['eid']
            ];
        }
        else {
            $sql = 'UPDATE employees SET start_date=:start_date::date,photo_id=:fid,status=:status,_date=now(),uid=:uid WHERE id=:id';
            $insert = [
                ':start_date'=>$data['start_date'],
                ':fid'=>$data['fid'],
                ':status'=>$data['status'],
                ':uid'=>$data['uid'],
                ':id'=>$data['eid']
            ];
        }
        try {
            if (!$this->updateProfile($data)) throw new Exception("Failed to update employee profile.");

            $pntr = $this->dbh->prepare($sql);
            if (!$pntr->execute($insert)) throw new Exception("Failed to update employee");
            return true;
        }
        catch (Exception $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }
    }

    /**
     * Adds a new attendance record to the given employee
     * @param Array $data The data array in the form; `['eid'=>string,'occ_date'=>ISODate,'arrive_time'=>Time,'leave_time'=>Time,
     * 'absent'=>Boolean,'excused'=>Boolean,'description'=>String,uid=>String]`
     * @return Boolean True on sucess, false otherwise.
     */
    public function addAttendanceRecord (Array $data) {
        $sql = 'INSERT INTO missed_time VALUES (
            :id,:eid,:occ_date,:absent::boolean,:arrive_time::time,:leave_time::time,:description,:excused::boolean,:uid,now()
            )';
        try {
            $pntr = $this->dbh->prepare($sql);
            //For issue #57
            if (!empty($data['begin_date']) && !empty($data['end_date'])) {
                $begin = new DateTime($data['begin_date']);
                $end = new DateTime($data['end_date']);
                $end = $end->modify( '+1 day' );
                $interval = new DateInterval('P1D');
                $period = new DatePeriod($begin,$interval,$end);
                $this->dbh->beginTransaction();
                foreach($period as $date) {
                    $insert = [
                        ':id'=>uniqid(),
                        ':eid'=>$data['eid'],
                        ':occ_date'=>$date->format('Y/m/d'),
                        ':arrive_time'=>$data['arrive_time'],
                        ':leave_time'=>$data['leave_time'],
                        ':absent'=>$data['absent'],
                        ':description'=>$data['description'],
                        ':uid'=>$data['uid'],
                        ':excused'=>$data['excused']
                    ];
                    $pntr->execute($insert);
                }
                $this->dbh->commit();
                return true;
            }
            elseif (!empty($data['begin_date'])) {
                $insert = [
                    ':id'=>uniqid(),
                    ':eid'=>$data['eid'],
                    ':occ_date'=>$data['begin_date'],
                    ':arrive_time'=>$data['arrive_time'],
                    ':leave_time'=>$data['leave_time'],
                    ':absent'=>$data['absent'],
                    ':description'=>$data['description'],
                    ':uid'=>$data['uid'],
                    ':excused'=>$data['excused']
                ];
                if (!$pntr->execute($insert)) throw new Exception("Insert faile: {$sql}");
                return true;
            }
            elseif (!empty($data['end_date'])) {
                $insert = [
                    ':id'=>uniqid(),
                    ':eid'=>$data['eid'],
                    ':occ_date'=>$data['end_date'],
                    ':arrive_time'=>$data['arrive_time'],
                    ':leave_time'=>$data['leave_time'],
                    ':absent'=>$data['absent'],
                    ':description'=>$data['description'],
                    ':uid'=>$data['uid'],
                    ':excused'=>$data['excused']
                ];
                if (!$pntr->execute($insert)) throw new Exception("Insert faile: {$sql}");
                return true;
            }
            else {
                throw new Exception('Missing date data value');
            }
        }
        catch (PDOException $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }
        catch (Exception $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }
    }

    /**
     * Retrieves an attendance record by it's given ID
     * @param String $id The ID of the record to retrieve
     * @return Array The record in an indexed array format, or false on failure.
     */
    public function getAttendanceByID ($id) {
        $sql = 'SELECT * FROM missed_time WHERE id = ?';
        try {
            $pntr = $this->dbh->prepare($sql);
            if (!$pntr->execute([$id])) throw new Exception("Select failed: {$sql}");
            return $pntr->fetchAll(PDO::FETCH_ASSOC)[0];
        }
        catch (PDOException $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }
        catch (Exception $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }
    }

    /**
     * Amends an attendance record with given data
     * @param Array $data The data array in the form 
     * `['id'=>String,'occ_date'=>dateISO,'absent'=>Boolean,'arrive_time'=>Time,'leave_time'=>Time,'description'=>String,'excused'=>Boolean,'uid'=>String]`
     * @return Boolean True on success, false otherwise.
     */
    public function amendAttendanceRecord (Array $data) {
        $sql = 
            'UPDATE missed_time 
             SET occ_date=:occ_date,absent=:absent::boolean,arrive_time=:arrive_time::time,
             leave_time=:leave_time::time,description=:description,excused=:excused::boolean,uid=:uid
             WHERE id = :id';
        try {
            $pntr = $this->dbh->prepare($sql);
            $insert = [
                ':id'=> $data['id'],
                ':occ_date'=>$data['occ_date'],
                ':absent'=>$data['absent'],
                ':arrive_time'=>$data['arrive_time'],
                ':leave_time'=>$data['leave_time'],
                ':description'=>$data['description'],
                ':excused'=>$data['excused'],
                ':uid'=>$data['uid']
            ];
            if (!$pntr->execute($insert)) throw new Exception("Update failed: {$sql}");
            return true;
        }
        catch (PDOException $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }
        catch (Exception $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }
    }

    /**
     * Removes an attendance record from the database table
     * 
     * Removes attendance record from the attendance table
     * @param String $id The ID of the record to remove
     * @return Boolean True on success, false otherwise
     */
    public function removeAttendanceRecord ($id) {
        $sql = 'DELETE FROM missed_time WHERE id = ?';
        try {
            $pntr = $this->dbh->prepare($sql);
            if (!$pntr->execute([$id])) throw new Exception('Failed to remove record: '.$id);
            return true;
        }
        catch (Exception $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }
    }
}
