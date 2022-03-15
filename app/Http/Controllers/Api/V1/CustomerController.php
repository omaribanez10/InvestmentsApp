<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Models\Investment;
use Illuminate\Http\Request;
use App\Utils\Util;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CredentialsMailable;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customer = DB::transaction(function () use($request){
                $fields = $request->validate([
                    'name' => 'required|string',
                    'last_name' => 'required|string',
                    'phone' => 'required|numeric',
                    'address' => 'required|string',
                    'city' => 'required|string',
                    'department' => 'required|string',
                    'country' => 'required|string',
                    'document_number' => 'required|numeric|unique:customers',
                    'file_document' => 'required|string',
                    'email' => 'required|string|unique:users,email',
                    'id_rol' => 'required|numeric',

                    //Datos investment
                    'amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
                    'consignment_file' => 'required|string',
                    'id_currency' => 'required|numeric',
                    'id_payment_method' => 'required|numeric',
                ]);
                
               
                $customer_level = Util::validateCustomerLevel($fields['amount']);
                $password = Util::generatePassword();
                $personal_code = Util::generatePersonalCode($fields['email']);

                $user = User::create([
                    'name' => $fields['name']." ".$fields['last_name'],
                    'email' => $fields['email'],
                    'password' => bcrypt($password),
                    'id_rol' => $fields['id_rol'],
                    'personal_code' => $personal_code
        
                ]);
        
                $customer = Customer::create([
                    'id_user' => $user->id,
                    'name' => $fields["name"],
                    'last_name' => $fields["last_name"],
                    'phone' => $fields["phone"],
                    'address' => $fields["address"],
                    'city' => $fields["city"],
                    'department' => $fields["department"],
                    'country' => $fields["country"],
                    'document_number' => $fields["document_number"],
                    'file_document' => $fields["file_document"],
                    'description_ind' => $request->description_ind,
                    'file_rut' => $request->file_rut,
                    'business' => $request->business,
                    'position_business' => $request->position_business,
                    'antique_bussiness' => $request->antique_bussiness,
                    'type_contract' => $request->type_contract,
                    'work_certificate' => $request->work_certificate,
                    'pension_fund' => $request->pension_fund,
                    'especification_other' => $request->especification_other,
                    'account_number' => $request->account_number,
                    'account_type' => $request->account_type,
                    'bank_name' => $request->bank_name,
                    'account_certificate' => $request->account_certificate,
                    'document_third' => $request->document_third,
                    'name_third' => $request->name_third,
                    'letter_authorization_third' => $request->letter_authorization_third,
                    'kinship_third' => $request->kinship_third,
                    'rut_third' => $request->rut_third,
                    'id_document_type' => $request->id_document_type,
                    'id_economic_activity' => $request->id_economic_activity,
                    'id_bank_account' => $request->id_bank_account,
                    'customer_level' => $customer_level
                ]);


                $investment = Investment::create([
                    'id_customer' => $customer->id,
                    'amount' => $fields['amount'],
                    'consignment_file' => $fields['consignment_file'],
                    'id_currency' => $fields['id_currency'],
                    'other_currency' => $request->other_currency,
                    'id_payment_method' => $fields['id_payment_method'],
                    'investment_date' => date('Y-m-d h:i:s'),
                ]);

                $investment->save();

                //ENVIAR PAGARÉ
                //$ruta =  public_path().'\pdfs\\';
                //$nombre_inscrito = $request->input('nombres')." ". $request->input('apellidos');
                //$nombre_sin_espacios = mb_strtoupper(str_replace(" ", "", $nombre_inscrito)); 
                //$nombreArchivo = "certificado"."-".$nombre_sin_espacios.".pdf";
                $adminLogged = User::find(1);
                $customer_fullname = $fields['name']." ".$fields['last_name'];
                $dataAdmin["email"] = $adminLogged->email;
                $dataAdmin["title"] = "Pagaré del cliente ".$fields['document_number']." ".$customer_fullname;
                $dataAdmin["amount"] = $fields['amount'];
                $dataAdmin["bank_promissor_number"] = $investment->id;
                $dataAdmin["document_number"] = $fields['document_number'];
                $dataAdmin["customer_name"] = $customer_fullname;
                
                Util::sendEmailWithPDFFile('Emails.bank_promissor_note', $dataAdmin);

                //ENVIAR CREDENCIALES
                $data["email"] =  $fields['email'];
                $data["title"] = "Te damos la bienvenida a VIP World Trading";
                $data["code"] = $personal_code; 
                $data["password"] = $password;
                
                $mail = new CredentialsMailable($data);
                Mail::to($data["email"])->send($mail);
  
                return $customer;
            
        }, 3); 
        
        return Util::setResponseJson(201, $customer);
  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
