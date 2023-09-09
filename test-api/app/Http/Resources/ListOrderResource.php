<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class ListOrderResource extends JsonResource
{
   /**
    * Transform the resource collection into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array
    */
   public function toArray($request)
   {
        $created_at = $this->created_at;
        if (! empty($created_at)) {
            $start_date_today = date("Y-m-d 00:00:00");
            $end_date_today = date("Y-m-d 23:59:59");
            $start_date_yesterday = date("Y-m-d", strtotime("-1 days")). ' 00:00:00';
            $end_date_yesterday = date("Y-m-d", strtotime("-1 days")). ' 23:59:59';
            $start_date_this_week = date("Y-m-d", strtotime("this week")). ' 00:00:00';
            $end_date_this_week = date("Y-m-d 23:59:59");
            $start_date_last_week = date("Y-m-d", strtotime("monday last week")). ' 00:00:00';
            $end_date_last_week = date("Y-m-d", strtotime("monday last week + 6 days")). ' 23:59:59';
            $start_date_this_month = date("Y-m-d", strtotime("first day of this month")). ' 00:00:00';
            $end_date_this_month = date("Y-m-d 23:59:59");
            $start_date_last_month = date("Y-m-d", strtotime("first day of previous month")). ' 00:00:00';
            $end_date_last_month = date("Y-m-d", strtotime("last day of previous month")). ' 23:59:59';
            // dd($created_at <= $end_date_today);
            if($start_date_today <= $created_at &&  $created_at <= $end_date_today){
                $category_time = 1;
            }elseif($start_date_yesterday <= $created_at &&  $created_at <= $end_date_yesterday){
                $category_time = 2;
            }elseif($start_date_this_week <= $created_at &&  $created_at <= $end_date_this_week){
                $category_time = 3;
            }elseif($start_date_last_week <= $created_at &&  $created_at <= $end_date_last_week){
                $category_time = 4;
            }elseif($start_date_this_month <= $created_at &&  $created_at <= $end_date_this_month){
                $category_time = 5;
            }elseif($start_date_last_month <= $created_at &&  $created_at <= $end_date_last_month){
                $category_time = 6;
            }else{
                $category_time = '0';
            } 
        }
        if ($this->type_order === 1) {
            $type_order = 'Dine In';
        }else{
            $type_order = 'Take Away';
        }
        if ($this->payment_method === 1) {
            $payment_method = 'Cash';
        } else {
            $payment_method = 'Other';
        }
        return [
            "id_order" => $this->id_order,
            "no_transaction" => $this->no_transaction,
            "name_order" => $this->name_order,
            "type_order" => $type_order,
            "total_amount" => $this->total_amount,
            "payment_method" => $payment_method,
            "created_at" => $this->created_at,
            "payment_status" =>  $this->payment_status,
            "category_time" => $category_time
        ];
   }
}
