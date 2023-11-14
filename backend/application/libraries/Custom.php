<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom {

        public function weekOfMonth($date)
        {
            $selisih = 6/*jumat*/-(date('w',strtotime($date))+1);
            $date =$selisih>=0?date('Y-m-d', strtotime(date('Y-m-d',strtotime($date)). ' + '.$selisih.' days')):date('Y-m-d', strtotime(date('Y-m-d',strtotime($date)). ' + '.(5-$selisih).' days'));
            $dateSub=date('Y-m-d',strtotime($date.' - '.(date('j',strtotime($date))-1). ' days'));
            // echo $dateSub;
            // echo strtotime(date('Y-m-d'). ' + '.$selisih.' days');
            return date('W',strtotime($date))-date('W',strtotime($dateSub))+1>0?date('W',strtotime($date))-date('W',strtotime($dateSub))+1:(int)date('W',strtotime($date));
        }
}