<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extract extends Model
{
    use HasFactory;
    protected $table = 'extracts';

    protected $fillable = [
        'id_customer',
        'total_disbursed',
        'total_reinvested',
        'profitability_percentage',
        'grand_total_invested',
        'registered_by',
        'status',
        'month'
    ];

    public static function getExtractByCustomerAndMonth($customer, $month) {

        return Extract::where('id_customer', $customer)->where('month', $month)->get();

    }

    public static function getExtractByCustomer($customer) {

        return Extract::join('extracts_details', 'extracts_details.id_extract', '=', 'extracts.id')
            ->join('investments', 'investments.id', '=', 'extracts_details.id_investment')
            ->select('investments.investment_date AS fecha_consignacion', 'extracts_details.profitability_start_date AS fecha_inicio', 
            'investments.id AS numero_pagare','extracts_details.investment_amount AS capital_inicial_mes',
            'investments.amount AS valor_inversion', 'extracts_details.investment_return' )
            ->where('extracts.id_customer', $customer)->get();

    }

    public static function getExtractByCustomerAndStatus($customer) {

        return Extract::where('id_customer', $customer)->where('status', 1)->get();

    }

    public static function getExtractByIdCustomer($customer) {

        return Extract::where('id_customer', $customer)->get();

    }

    public static function getExtractByCustomerAndDate($date, $customer) {
            

        return Extract::where('id_customer', $customer)->where('created_at', 'LIKE', '%'.$date.'%')->get();

    }

    
    public function extractDetail (){

        return $this->hasMany(ExtractDetail::class, 'id_extract', 'id');
    }

    public function customer (){

        return $this->belongsTo(Customer::class, 'id_customer', 'id');
    }
}
