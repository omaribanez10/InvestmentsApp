<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Models\Investment;
use App\Models\ModelHasRol;
use Illuminate\Http\Request;
use App\Utils\Util;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CredentialsMailable;
use App\Http\Resources\V1\CustomerResource;
use App\Http\Resources\V1\CustomerCollection;

use App\Http\Traits\InvestmentTrait;

class CustomerController extends Controller
{

    use InvestmentTrait;


    public function index(){      
        
        return new CustomerCollection(Customer::all());
        
    }

  
    public function create()
    {
        //
    }

   
    public function store(Request $request)
    {
        $customer = DB::transaction(function () use($request){

            
                $rules = [
                    'name' => 'required|string',
                    'last_name' => 'required|string',
                    'phone' => 'required|numeric',
                    'address' => 'required|string',
                    'city' => 'required|string',
                    'department' => 'required|string',
                    'country' => 'required|string',
                    'document_number' => 'required|numeric|unique:customers',
                    'file_document' => 'required|file',
                    'email' => 'required|email|unique:users,email',
                    /*'id_rol' => 'required|numeric',*/
                    'registered_by' => 'required|numeric',
                  ];

             
                $messages = [
                    'document_number.unique' => 'El numero de documento ya se encuentra registrado en VIP WORLD TRADING',
                    'email.unique' =>'El correo ya se encuentra registrado en el sistema',
                ];

                $fields=$request->validate($rules,$messages);
                //$this->validate($request, $rules, $messages);
           
                //Se calcula la clasificación del cliente dependiendo del monto de la inversión.
                $customer_type = Util::validateCustomerLevel($request->amount);
              
                //Se genera una contraseña aleatoria.
                $password = Util::generatePassword();
                
                //Se genera un código personal único, a partir del correo del cliente.
                $personal_code = Util::generatePersonalCode($fields['email']);

                //Se crea el usuario
                $user = User::create([
                    'name' => $fields['name']." ".$fields['last_name'],
                    'email' => $fields['email'],
                    'password' => bcrypt($password),
                    'personal_code' => $personal_code,
                    'user_type' =>2
                ]);

                $rol = ModelHasRol::create([
                    'role_id' => 2,
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id
                   
                ]);
                
                $file_document=NULL;
                if($request->hasFile("file_document")){
                    $file=$request->file("file_document");
                    
                    $file_document = "documento_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/documentos_identidad/".$file_document);
                    copy($file, $ruta);
                }
              
                $work_certificate=NULL;
                if($request->hasFile("work_certificate")){
                    $file=$request->file("work_certificate");
                    
                    $work_certificate = "certificado_laboral_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/certificados_laborales/".$work_certificate);
                    copy($file, $ruta);
                }

                $account_certificate=NULL;
                if($request->hasFile("account_certificate")){
                    $file=$request->file("account_certificate");
                    
                    $account_certificate = "certificado_cuenta_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/certificados_cuenta/".$account_certificate);
                    copy($file, $ruta);
                }

                $letter_authorization_third=NULL;
                if($request->hasFile("letter_authorization_third")){
                    $file=$request->file("letter_authorization_third");
                    
                    $letter_authorization_third = "carta_autorizacion_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/certificados_cuenta/".$letter_authorization_third);
                    copy($file, $ruta);
                }

                $file_rut=NULL;
                if($request->hasFile("file_rut")){
                    $file=$request->file("file_rut");
                    
                    $file_rut = "rut_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/rut/".$file_rut);
                    copy($file, $ruta);
                }

                $rut_third=NULL;
                if($request->hasFile("rut_third")){
                    $file=$request->file("rut_third");
                    
                    $rut_third = "rut_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/rut_terceros/".$rut_third);
                    copy($file, $ruta);
                }
            
                //Se crea el cliente.
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
                    'file_document' => $file_document,
                    'description_ind' => $request->description_ind,
                    'file_rut' => $file_rut,
                    'business' => $request->business,
                    'position_business' => $request->position_business,
                    'antique_bussiness' => $request->antique_bussiness,
                    'type_contract' => $request->type_contract,
                    'work_certificate' => $work_certificate,
                    'pension_fund' => $request->pension_fund,
                    'especification_other' => $request->especification_other,
                    'account_number' => $request->account_number,
                    'account_type' => $request->account_type,
                    'bank_name' => $request->bank_name,
                    'account_certificate' => $account_certificate,
                    'document_third' => $request->document_third,
                    'name_third' => $request->name_third,
                    'letter_authorization_third' => $letter_authorization_third,
                    'kinship_third' => $request->kinship_third,
                    'rut_third' => $rut_third,
                    'id_document_type' => $request->id_document_type,
                    'id_economic_activity' => $request->id_economic_activity,
                    'id_bank_account' => $request->id_bank_account,
                    'id_customer_type' => $customer_type,
                    'registered_by' => $fields["registered_by"],

                ]);
                
                //Se usa el Trait InvestmentTrait para guardar la información de la inversión
                $this->storeInvestment($request, $customer->id);

               
                $dataCustomer["email"] =  $fields['email'];
                $dataCustomer["title"] = "Te damos la bienvenida a VIP World Trading";
                $dataCustomer["code"] = $personal_code; 
                $dataCustomer["password"] = $password;
                $dataCustomer["name"] = $fields["name"]." ".$fields["last_name"];
                
                //Se envían las credenciales del cliente al correo
                Util::sendCredentialsEmail($dataCustomer);
              
                
                return $customer;
            
        }, 3); 
        
        return Util::setResponseJson(201, 'Se ha registrado el cliente y la inversión de exitosamente.');
  
    }


    public function show($id)
    {
        return new CustomerResource(Customer::find($id));
    }

    
    public function edit($customer)
    {
        $customer = new CustomerResource(Customer::find($customer));
        return Util::setJSONResponseUniqueData($customer);
    
    }

   
    public function update(Request $request, $customer)
    {   
        $customer_response = DB::transaction(function () use($request, $customer){
                $fields = $request->validate([
                    'name' => 'required|string',
                    'last_name' => 'required|string',
                    'phone' => 'required|numeric',
                    'address' => 'required|string',
                    'city' => 'required|string',
                    'department' => 'required|string',
                    'country' => 'required|string',
                    'document_type' => 'required|numeric',
                    'document_number' => 'required|numeric',
                    'file_document' => 'required',
                    'email' => 'required|email',
                    /*'id_rol' => 'required|numeric',*/
                    /*'updated_by' => 'required|numeric',*/
                ]);

                $contract_file=$request->contract_file;
                if($request->hasFile("contract_file")){
                    $file=$request->file("contract_file");
                    
                    $contract_file = "contrato_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/contratos/".$contract_file);
                    copy($file, $ruta);
                }
                

                $file_document=$fields['file_document'];
                if($request->hasFile("file_document")){
                    $file=$request->file("file_document");
                    
                    $file_document = "documento_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/documentos_identidad/".$file_document);
                    copy($file, $ruta);
                }
                
                $work_certificate=$request->work_certificate;
                if($request->hasFile("work_certificate")){
                    $file=$request->file("work_certificate");
                    
                    $work_certificate = "certificado_laboral_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/certificados_laborales/".$work_certificate);
                    copy($file, $ruta);
                }

                $account_certificate=$request->account_certificate;
                if($request->hasFile("account_certificate")){
                    $file= $request->file("account_certificate");
                    
                    $account_certificate = "certificado_cuenta_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/certificados_cuenta/".$account_certificate);
                    copy($file, $ruta);
                }
                $account_certificate_third=$request->account_certificate_third;
                if($request->hasFile("account_certificate_third")){
                    $file= $request->file("account_certificate_third");
                    
                    $account_certificate = "certificado_cuenta_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/certificados_cuenta/".$account_certificate);
                    copy($file, $ruta);
                }
                
                $account_certificate = is_null($account_certificate) || empty($account_certificate) ? $account_certificate_third : $account_certificate;

                $letter_authorization_third=$request->letter_authorization_third;
                if($request->hasFile("letter_authorization_third")){
                    $file=$request->file("letter_authorization_third");
                    
                    $letter_authorization_third = "carta_autorizacion_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/certificados_cuenta/".$letter_authorization_third);
                    copy($file, $ruta);
                }

                $file_rut=$request->file_rut;
                if($request->hasFile("file_rut")){
                    $file=$request->file("file_rut");
                    
                    $file_rut = "rut_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/rut/".$file_rut);
                    copy($file, $ruta);
                }

                $rut_third=$request->rut_third;
                if($request->hasFile("rut_third")){
                    $file=$request->file("rut_third");
                    
                    $rut_third = "rut_".$fields["document_number"].".".$file->guessExtension();
                    $ruta = public_path("archivos/rut_terceros/".$rut_third);
                    copy($file, $ruta);
                }

                $user = User::find($request->id_user);
                $user->email=$request->email;
                $user->save();

                $customer = Customer::find($customer);
                $customer->name = $fields['name'];
                $customer->last_name=  $fields['last_name'];
                $customer->id_document_type= $fields['document_type'];
                $customer->document_number=$fields['document_number'];
                $customer->phone=$fields['phone'];
                $customer->address=$fields['address'];
                $customer->city=$fields['city'];
                $customer->department=$fields['department'];
                $customer->country=$fields['country'];
                $customer->file_document=$file_document;
                $customer->description_ind=$request->description_ind;
                $customer->file_rut=$file_rut;
                $customer->business=$request->business;
                $customer->position_business=$request->position_business;
                $customer->antique_bussiness=$request->antique_bussiness;
                $customer->type_contract=$request->type_contract;
                $customer->work_certificate=$request->work_certificate;
                $customer->pension_fund=$request->pension_fund;
                $customer->especification_other=$request->especification_other;
                $customer->contract_file=$contract_file;
                $customer->account_number=$request->account_number;
                $customer->account_type=$request->account_type;
                $customer->bank_name=$request->bank_name;
                $customer->account_certificate=$account_certificate;
                $customer->document_third=$request->document_third;
                $customer->name_third=$request->name_third;
                $customer->letter_authorization_third=$letter_authorization_third;
                $customer->kinship_third=$request->kinship_third;
                $customer->rut_third=$rut_third;
                $customer->id_economic_activity=$request->economic_activity;
                $customer->id_bank_account=$request->bank_account;
                $customer->update();

                return $customer;
                
            }, 3); 
            
            return Util::setResponseJson(201, 'Se ha actualizado el cliente exitosamente.');

    }

   
    public function destroy(Customer $customer)
    {
       
    }

    
    public function getCustomers($param){
        
        return new CustomerCollection(Customer::searchCustomerByParams($param));

    }

    public function getCustomersbyCustomerType(Request $request){
       
        $fields = $request->validate([
            'param' => 'required|string',
            'id_customer_type' => 'required|numeric',

        ]);

        $customers = Customer::searchCustomerByParamsAndCustomerType($fields['param'], $fields['id_customer_type']);

        if($customers){  
            return new CustomerCollection($customers);   
        } else {  return array();  }

       
    }

    public function getCustomersbyCustomerPremium(Request $request){
       
        $fields = $request->validate([
            'param' => 'required|string',
            'id_customer_type' => 'required|numeric',
        ]); 

        $customer = Customer::searchCustomerByParamsAndCustomerType($fields['param'], 3);
        
        if($customer){  return new CustomerResource($customer);  } else {  return array();  }

    }

    public function chargeCustomerContract(Request $request){

        $contract_response = DB::transaction(function () use($request){
           
            $fields = $request->validate([
                'file' => 'required|file',
                'id_user' => 'required|numeric',
            ]); 
    
            $customer = Customer::where('id_user',$fields['id_user'])->first();
            $contrato=NULL;
    
            if($request->hasFile("file")){
                $file=$request->file("file");
                
                $contrato = "contrato_".$customer->document_number.".".$file->guessExtension();
                $ruta = public_path("archivos/contratos/".$contrato);
                copy($file, $ruta);
            }   

            $customer->contract_file = $contrato;
            $customer->update();

            return $contrato;
        }, 3); 

        return Util::setResponseJson(201, 'Se ha cargado el contrato exitosamente, el documento se guardó con el nombre de '.$contract_response);
 
    }

    public function chargeSARLAFTDocument(Request $request){

        $SARLAFT_response = DB::transaction(function () use($request){
           
            $fields = $request->validate([
                'file' => 'required|file',
                'id_user' => 'required|numeric',
            ]); 
    
            $customer = Customer::where('id_user',$fields['id_user'])->first();
            $documento=NULL;
    
            if($request->hasFile("file")){
                $file=$request->file("file");
                
                $documento = "SARLAFT_".$customer->document_number.".".$file->guessExtension();
                $ruta = public_path("archivos/SARLAFT/".$documento);
                copy($file, $ruta);
            }   
             
            $customer->sarlaft_file = $documento;
            $customer->update();

            return $documento;
        }, 3); 

        return Util::setResponseJson(201, 'Se ha cargado el documento SARLAFT exitosamente, el documento se guardó con el nombre de '.$SARLAFT_response);
 
    }

    public function changeProfilePicture(Request $request){

        $photo_response = DB::transaction(function () use($request){
           
            $fields = $request->validate([
                'file' => 'required|file',
                'id_user' => 'required|numeric',
            ]); 
    
            $customer = Customer::where('id_user',$fields['id_user'])->first();
            $photo=NULL;
    
            if($request->hasFile("file")){
                $file=$request->file("file");
                
                $photo = "foto".$customer->document_number.".".$file->guessExtension();
                $ruta = public_path("archivos/fotos/".$photo);
                copy($file, $ruta);
            }   
             
            $customer->photo = $photo;
            $customer->update();

            return $photo;
        }, 3); 

        return Util::setResponseJson(201, 'Se ha cargado la foto exitosamente');

    }
 
}
