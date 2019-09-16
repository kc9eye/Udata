<?php
/* This file is part of UData.
 * Copyright (C) 2019 Paul W. Lane <kc9eye@outlook.com>
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
class Review extends Employee {
    const IN_REVIEW = 1;
    const NOT_IN_REVIEW = 0;
    const DATA_TIMEFRAME = '6 months';

    private $review;
    public $status;

    public function __construct (PDO $dbh, $eid) {
        parent::__construct($dbh, $eid);
        $this->review = array();
        $this->status = $this->reviewStatus();
        if ($this->status == self::IN_REVIEW) $this->setReviewData();
    }

    public function reviewStatus () {
        $sql = 'SELECT count(*) FROM reviews WHERE end_date >= now() AND eid = ?';
        try {
            $pntr = $this->dbh->prepare($sql);
            if (!$pntr->execute([$this->eid])) throw new Exception(print_r($pntr->errorInfo(),true));
            $result = $pntr->fetchAll(PDO::FETCH_ASSOC)[0]['count'];
            if ($result != self::IN_REVIEW || $result != self::NOT_IN_REVIEW) throw new Exception("Result returned an invalid value");
            return $result;
        }
        catch (Exception $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }
    }

    protected function setReviewData () {
        //Get the raw review table data
        $sql = 'SELECT * FROM reviews WHERE end_date >= now() AND eid = ?';
        try {
            $pntr = $this->dbh->prepare($sql);
            if (!$pntr->execute([$this->eid])) throw new Exception(print_r($pntr->errorInfo(),true));
            $this->review['raw_review'] = $pntr->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (Exception $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }

        //Get the review attendance data
        $sql = 'SELECT * FROM missed_time WHERE eid = :eid AND occ_date >= (now() - :timeframe) ORDER BY occ_date DESC';
        try {
            $pntr = $this->dbh->prepare($sql);
            if (!$pntr->execute([':eid'=>$this->eid,':timeframe'=>self::DATA_TIMEFRAME])) throw new Exception(print_r($pntr->errorInfo(),true));
            $this->review['attendance'] = $pntr->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (Exception $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }

        //Get the comments data
        $sql = 
            "SELECT
                id,
                (SELECT firstname||' '||lastname FROM user_accts WHERE id = a.uid) as author,
                _date as date,
                comments
            FROM supervisor_comments as a
            WHERE eid = :eid AND _date >= (now() - :timeframe)
            ORDER BY _date DESC";
        try {
            $pntr = $this->dbh->prepare($sql);
            if (!$pntr->execute([':eid'=>$this->eid,':timeframe'=>self::DATA_TIMEFRAME])) throw new Exception(print_r($pntr->errorInfo(),true));
            $this->review['comments'] = $pntr->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (Exception $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }

        //Get others reviews
        $sql = 'SELECT * FROM review_comments WHERE revid = ?';
        try {
            $pntr = $this->dbh->prepare($sql);
            if (!$pntr->execute([$this->review['raw_review']['id']])) throw new Exception(print_r($pntr->errorInfo(),true));
            $this->review['review_comments'] = $pntr->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (Exception $e) {
            trigger_error($e->getMessage(),E_USER_WARNING);
            return false;
        }
    }

    /**
     * Returns the review start date;
     * @return String The review start data.
     */
    public function getStartDate () {
        return $this->review['raw_review']['start_date'];
    }

    /**
     * Returns the review end date;
     * @return String The review end date
     */
    public function getEndDate () {
        return $this->review['raw_review']['end_date'];
    }

    /**
     * Returns an array of attendance data in the Review::DATA_TIMEFRAME timeframe
     * @return Array In the form 
     * `['id'=>string,'eid'=>string,'occ_date'=>string,'absent'=>bool,'arrive_time'=>string,
     *   'leave_time'=>string,'description'=>string,'excused'=>bool,'uid'=>string,'_date'=>string]`
     */
    public function getReviewAttendance () {
        return $this->review['attendance'];
    }

    /**
     * Returns an array of management comment data
     * @return Array In the form: 
     * `['id'=>string,'author'=>string,'_date'=>string,'comments'=>string]`
     */
    public function getReviewManagementComments () {
        return $this->review['comments'];
    }
}