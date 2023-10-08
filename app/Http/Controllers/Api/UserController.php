<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RegisterFileResource;
use App\Http\Resources\RegistrationResource;
use App\Http\Resources\UserResource;
use App\Models\ClassModel;
use App\Models\Invoice;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function test(Request $req) {
        $period = self::getGroupedPeriod($req->get('start'), $req->get('end'));
        $months = [];
        $list = [];
        foreach ($period as $month) {
            $monthFormat = Carbon::parse($month['start'])->format('Y-m');
            $months[] = $monthFormat;
            $data = DB::table('invoices', 'i')
                    ->join('users AS u', 'u.id', 'i.id_user')
                    ->leftJoin('students AS st', function($st) {
                        $st->on('st.id_user', 'u.id')
                            ->where('st.deleted', '!=', 1);
                    })
                    ->where('i.status', '!=', 'C')
                    ->where('u.deleted', '!=', 1)
                    ->whereBetween('i.expires_at', [$month['start'], $month['end']])
                    ->groupBy('u.id')
                    ->selectRaw("
                        u.id,
                        u.name,
                        u.status,
                        DATE_FORMAT(u.created_at, '%Y-%m') AS created_at,
                        GROUP_CONCAT(DISTINCT st.name) AS students,
                        sum(if(i.status = 'P', i.value + i.fee + i.added, 0)) as paid,
                        sum(if(i.status = 'A', i.value + i.fee + i.added, 0)) as pending,
                        (
                            SELECT DATE_FORMAT(created_at, '%Y-%m')
                            FROM invoices
                            WHERE id_user = u.id
                                AND status != 'C'
                            ORDER BY created_at
                            LIMIT 1
                        ) AS last_invoice
                    ")
                    ->get()->toArray();

            $list[] = [
                'month' => $monthFormat,
                'data' => $data
            ];
        }
        // dd($list);
        $users = [];
        foreach ($list as $month) {
            foreach ($month['data'] as $data) {
                $users[] = [
                    'id' => $data->id,
                    'name' => $data->name,
                    'status' => $data->status,
                    'created_at' => $data->created_at,
                    'last_invoice' => $data->last_invoice,
                    'students' => $data->students
                ];
            }
        }

        $result = [];
        foreach ($users as $user) {
            $result[$user['id']]['user'] = $user;
            foreach ($list as $month) {
                $dataUser = array_search($user['id'], array_column((array)$month['data'], 'id'));
                $values = null;
                if ($dataUser !== false) {
                    $arr = ((array)$month['data'])[$dataUser];
                    $values = [
                        'paid' => floatval($arr->paid),
                        'pending' => floatval($arr->pending)
                    ];
                } else if ($user['created_at'] >= $month['month']) {
                    $values = 'nonexistent';
                } else if ($user['status'] == 'I' && $user['last_invoice'] < $month['month']) {
                    $values = 'inactive';
                }
                $result[$user['id']]['data'][$month['month']] = $values;
            }
        }

        $response = [];
        foreach ($result as $item) {
            $response[] = $item;
        }

        return response()->json(['data' => $response, 'months' => $months]);
    }

    public static function getGroupedPeriod(string $start, string $end) {
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);
        $monthsCount = $endDate->clone()->startOfMonth()->diffInMonths($startDate->clone()->startOfMonth());

        for ($i = 0; $i <= $monthsCount; $i++) {

            $start = $startDate->clone()->addMonthNoOverflow($i)->startOfMonth();
            $end = $startDate->clone()->addMonthNoOverflow($i)->endOfMonth();

            // if ($end->format('Y-m-d') > $endDate->format('Y-m-d')) {
            //     $end = $endDate->clone();
            // }

            $period[] = [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d')
            ];
        }

        return $period;
    }


    /**
     * list all
     * 
     * @return  json
     */
    public function list( Request $request ){
        try {

            $dataFilter = $request->all();
            $result = $this->usersService->listClients( $dataFilter );

            $response = [ 'status' => 'success', 'data' => UserResource::collection($result) ];

        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    /**
     * get by key and value
     * 
     * @return  json
     */
    public function get( Request $request ){

        try {

            $dataFilter = $request->all();
            $result = $this->usersService->get( $dataFilter );
    
            $response = [ 'status' => 'success', 'data' => new UserResource($result) ];
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response ); 
    }

    /**
     * get by id
     * 
     * @return  json
     */
    public function getById( $id ){

        try {

            $result = $this->usersService->get( ['id' => $id] );
            $response = [ 'status' => 'success', 'data' => new UserResource($result) ];

        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response ); 
    }

    /**
     * create
     * 
     * @return  json
     */
    public function createWithStudent( Request $request ){

        try {

            DB::beginTransaction();

            // create user
            $userValidData = $request->validate([
                'user_name'     => 'required|string',
                // 'user_email'    => 'required|string|email|unique:users,email',
                'user_email'    => [
                    'required',
                    'string',
                    'email',
                    Rule::unique('users', 'email')->where(fn (Builder $query) => $query->where('deleted', '!=', 1))
                ],
                // 'user_cpf'      => 'required|string|size:14|unique:users,cpf',
                'user_cpf' => [
                    'required',
                    'string',
                    'size:14',
                    Rule::unique('users', 'cpf')->where(fn (Builder $query) => $query->where('deleted', '!=', 1))
                ],
                'user_phone'    => 'required|string',
                'user_is_whatsapp' => 'nullable|integer',
                'user_password' => 'nullable|string',
                'user_password_confirmation' => 'nullable|string',
                'user_picture' => 'nullable|image',
                'send_password_mail' => 'nullable',

                'user_id_unit'  => 'required|integer|exists:units,id',
                'user_uf'       => 'required|string',
                'user_city'     => 'required|string',
                'user_district' => 'required|string',
                'user_street'   => 'required|string',
                'user_address_number'     => 'nullable|string',
                'user_address_complement' => 'nullable|string',
                'user_instagram' => 'nullable|string',
                'user_rg'         => 'required|string',
                'user_uf_orgao_exp'  => 'required|string',
                'user_orgao_exp'  => 'required|string',
                'user_profession' => 'nullable|string',
                'user_birthdate'  => 'required|string',
                'user_cep'        => 'required|string',
                'user_know_by'    => 'nullable|string',
            ]);
            $userData = $this->replaceKey($userValidData, 'user_');
            $userData['status'] = 'A';
            $this->usersService->verifyUserName($userData['name']);
            
            $user = $this->usersService->createUser($userData);

            // create student
            $studentValidData = $request->validate([
                'student_id_class'  => 'required|integer|exists:classes,id',
                'student_name'      => 'required|string',
                // 'student_nick_name' => 'required|string',
                'student_birthdate' => 'required|date',
                'student_picture'   => 'nullable|image',
            ]);
            $studentData = $this->replaceKey($studentValidData, 'student_');
            $studentData['status'] = 'A';
            $studentData['id_user'] = $user->id;
            $student = $this->studentsService->createStudent($studentData);
            
            $response = [ 'status' => 'success', 'data' => new UserResource($user) ];

            // send password reset mail
            if( !empty($userData['send_password_mail']) ) {
                $sendedMail = $this->passwordRecoveryService->sendMail( $user, true );
            }

            DB::commit();
        } catch ( ValidationException $e ){
            DB::rollBack();
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    /**
     * update
     * 
     * @return  json
     */
    public function update( Request $request, $id ){

        try {
            
            $validData = $request->validate([
                'name' => 'required|string',
                // 'email' => 'required|string|email|unique:users,email,'.$id,
                'email' => [
                    'required',
                    'string',
                    'email',
                    Rule::unique('users', 'email')->where(fn (Builder $query) => $query->where('deleted', '!=', 1)->where('id', '!=', $id))
                ],
                // 'cpf' => 'required|string|size:14|unique:users,cpf,'.$id,
                'cpf' => [
                    'required',
                    'string',
                    'size:14',
                    Rule::unique('users', 'cpf')->where(fn (Builder $query) => $query->where('deleted', '!=', 1)->where('id', '!=', $id))
                ],
                'phone' => 'required|string',
                'is_whatsapp'   => 'nullable|integer',
                'id_unit'  => 'required|integer|exists:units,id',
                'uf'       => 'required|string',
                'city'     => 'required|string',
                'district' => 'required|string',
                'street'   => 'required|string',
                'address_number'     => 'nullable|string',
                'address_complement' => 'nullable|string',
                'instagram' => 'nullable|string',
                'rg'         => 'required|string',
                'orgao_exp'  => 'required|string',
                'uf_orgao_exp'  => 'required|string',
                'profession' => 'nullable|string',
                'birthdate'  => 'required|string',
                'cep'        => 'required|string',
                'status'     => 'required|string'
            ]);

            $this->usersService->verifyUserName($validData['name']);
            $userData['is_whatsapp'] = empty($userData['is_whatsapp']) ? 0 : 1;
            $user = $this->usersService->find($id);
            $updated = $this->usersService->updateById( $id, $validData);
            if ($validData['phone'] != $user->phone || $validData['email'] != $user->email) {
                $this->clicksignService->replaceDocSigner($updated);
            }
            $response = [ 'status' => 'success', 'data' => new UserResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    /**
     * delete
     * 
     * @return  json
     */
    public function delete( $id, Request $request ){

        try {
            DB::beginTransaction();

            if( $request->input('confirmation') != 'confirmar' )
                throw ValidationException::withMessages(['Confirme a remoção do usuário']);

            $deleted = $this->usersService->softDelete( $id );
            $response = [ 'status' => 'success', 'data' => $deleted ];
            DB::commit();
        } catch ( ValidationException $e ){
            DB::rollBack();
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    /**
     * delete multiple
     * 
     * @return  json
     */
    public function deleteMultiple( Request $request ){

        try {
            DB::beginTransaction();

            if( $request->input('confirmation') != 'confirmar' )
                throw ValidationException::withMessages(['Confirme a remoção dos usuários']);

            $ids = $request->input('ids');
            if (empty($ids) || !is_array($ids))
                throw ValidationException::withMessages(['Não foi possível realizar esta ação']);

            $deleted = [];
            foreach ($ids as $id) {
                try {
                    $deleted[] = $this->usersService->softDelete( $id );
                } catch (ValidationException $e) {}
            }
            $response = [ 'status' => 'success', 'data' => $deleted ];
            DB::commit();
        } catch ( ValidationException $e ){
            DB::rollBack();
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function adminPasswordUpdate( Request $request, $id ) {
        try {
            
            $userData = $request->validate([
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|min:6',
            ]);
            
            if( $userData['password'] != $userData['password_confirmation'] )
                throw ValidationException::withMessages(['As senhas não conferem']);

            $updated = $this->usersService->updateById($id, ['password' => bcrypt($userData['password'])]);
            
            $response = [ 'status' => 'success', 'data' => new UserResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function adminUploadPicture( Request $request, $id ) {
        try {
            
            $userData = $request->validate([
                'picture' => 'required|image',
            ]);
            
            $user = $this->usersService->find($id);
            $updated = $this->usersService->uploadPicture($user, $userData['picture']);
            
            $response = [ 'status' => 'success', 'data' => new UserResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function adminSelfUpdate( Request $request ) {
        try {
            
            $user = auth('api')->user();
            $userData = $request->validate([
                'name' => 'required|string|min:1',
                'phone' => 'nullable|string',
                // 'email' => 'required|string|unique:users,email,'.$user->id,
                'email' => [
                    'required',
                    'string',
                    'email',
                    Rule::unique('users', 'email')->where(fn (Builder $query) => $query->where('deleted', '!=', 1)->where('id', '!=', $user->id))
                ],
            ]);
            
            $updated = $this->usersService->updateById($user->id, $userData);
            
            $response = [ 'status' => 'success', 'data' => new UserResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function passwordUpdate( Request $request ) {
        try {
            
            $user = auth('api')->user();

            $userData = $request->validate([
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|min:6',
            ]);
            
            if( $userData['password'] != $userData['password_confirmation'] )
                throw ValidationException::withMessages(['As senhas não conferem']);

            $updated = $this->usersService->updateById($user->id, ['password' => bcrypt($userData['password'])]);
            
            $response = [ 'status' => 'success', 'data' => new UserResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function addSignatory( $idUser ) {
        try {
            
            $user = $this->usersService->find($idUser);

            if( !empty($user->signer_key) )
                throw ValidationException::withMessages(['Este usuário já está cadastrado como signatário']);

            if( $user->status == 'MP' )
                throw ValidationException::withMessages(['Usuário com matrícula pendente, ao aprovar sua matrícula, ele será adicionado como signatário']);

            $updated = $this->clicksignService->createSignatory($user);
            $response = [ 'status' => 'success', 'data' => new UserResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }


    /**
     * create
     * 
     * @return  json
     */
    public function publicCreateWithStudent( Request $request ){

        try {

            DB::beginTransaction();

            // create user
            $userValidData = $request->validate([
                'user_name'     => 'required|string',
                // 'user_email'    => 'required|string|email|unique:users,email',
                'user_email'    => [
                    'required',
                    'string',
                    'email',
                    Rule::unique('users', 'email')->where(fn (Builder $query) => $query->where('deleted', '!=', 1))
                ],
                // 'user_cpf'      => 'required|string|size:14|unique:users,cpf',
                'user_cpf' => [
                    'required',
                    'string',
                    'size:14',
                    Rule::unique('users', 'cpf')->where(fn (Builder $query) => $query->where('deleted', '!=', 1))
                ],
                'user_phone'    => 'required|string',
                'user_is_whatsapp'   => 'nullable|integer',
                'user_password' => 'nullable|string',
                'user_password_confirmation' => 'nullable|string',
                'user_picture' => 'nullable|image',
                'send_password_mail' => 'nullable',

                'user_uf'       => 'required|string',
                'user_city'     => 'required|string',
                'user_district' => 'required|string',
                'user_street'   => 'required|string',
                'user_address_number'     => 'nullable|string',
                'user_address_complement' => 'nullable|string',
                'user_instagram' => 'nullable|string',
                'user_rg'         => 'required|string',
                'user_orgao_exp'  => 'required|string',
                'user_uf_orgao_exp'  => 'required|string',
                'user_profession' => 'nullable|string',
                'user_birthdate'  => 'required|string',
                'user_cep'        => 'required|string',
                'user_know_by'    => 'nullable|string',
            ]);
            $userData = $this->replaceKey($userValidData, 'user_');
            $userData['status'] = 'MP';
            $this->usersService->verifyUserName($userData['name']);
            $user = $this->usersService->publicCreateUser($userData);

            // create student
            $studentValidData = $request->validate([
                'student_id_class'  => 'required|integer|exists:classes,id',
                'student_name'      => 'required|string',
                // 'student_nick_name' => 'required|string',
                'student_birthdate' => 'required|date',
                'student_picture'   => 'nullable|image',
                'student_health_problem' => 'nullable|string',
                'student_food_restriction' => 'nullable|string',
                'student_in_school' => 'nullable|string',
                'student_school_time' => 'nullable|string',
            ]);
            $studentData = $this->replaceKey($studentValidData, 'student_');
            $studentData['status'] = 'MP';
            $studentData['id_user'] = $user->id;
            $student = $this->studentsService->createStudent($studentData);
            
            $class = ClassModel::find($studentData['id_class']);
            $user->update(['id_unit' => $class->id_unit]);

            // register files
            $fileValidData = $request->validate([
                'file_doc1' => 'required|file|mimes:png,jpg,jpeg,pdf,docx,xml,xls,xlsx,doc,txt,zip,rar,bin|max:4096',
                'file_doc2' => 'required|file|mimes:png,jpg,jpeg,pdf,docx,xml,xls,xlsx,doc,txt,zip,rar,bin|max:4096',
                'file_payment' => 'required|file|mimes:png,jpg,jpeg,pdf,docx,xml,xls,xlsx,doc,txt,zip,rar,bin|max:4096',
            ]);
            $files = $this->registerFilesSerivce->registerUpload( $user, $fileValidData );

            $response = [ 'status' => 'success', 'data' => new UserResource($user) ];

            // send password reset mail
            $sendMail = true;
            if( $sendMail ) {
                $sendedMail = $this->passwordRecoveryService->sendMail( $user, true );
            }

            DB::commit();
        } catch ( ValidationException $e ){
            DB::rollBack();
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    private function replaceKey( $data, $replacement ){
        $out = [];
        foreach( $data as $key => $value ){
            $newKey = str_replace($replacement, '', $key);

            if( !empty($value) && $value != 'null' )
                $out[$newKey] = $value;
        }
        return $out;
    }

    /**
     * get new registration by id
     * 
     * @return  json
     */
    public function getNewRegistration( $id ){

        try {

            $result = $this->usersService->get( ['id' => $id, 'status' => 'MP'] );
            $response = [ 'status' => 'success', 'data' => new RegistrationResource($result) ];

        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response ); 
    }

    /**
     * approve new registration by id
     * 
     * @return  json
     */
    public function approveRegistration( $id ){

        try {

            DB::beginTransaction();
            $result = $this->usersService->approveRegistration( $id );
            $response = [ 'status' => 'success', 'data' => new RegistrationResource($result) ];
            DB::commit();
        } catch ( ValidationException $e ){
            DB::rollBack();
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response ); 
    }


    /**
     * client self update
     * 
     * @return  json
     */
    public function selfUpdate( Request $request ){

        try {
            
            $user = auth('api')->user();

            $validData = $request->validate([
                'name' => 'required|string',
                // 'email' => 'required|string|email|unique:users,email,'.$id,
                // 'cpf' => 'required|string|size:14|unique:users,cpf,'.$id,
                'phone' => 'required|string',
                'is_whatsapp'   => 'nullable|integer',
                'uf'       => 'required|string',
                'city'     => 'required|string',
                'district' => 'required|string',
                'street'   => 'required|string',
                'address_number'     => 'nullable|string',
                'address_complement' => 'nullable|string',
                'instagram' => 'nullable|string',
                'rg'         => 'required|string',
                'orgao_exp'  => 'required|string',
                'profession' => 'nullable|string',
                'birthdate'  => 'required|string',
                'cep'        => 'required|string',
            ]);

            $this->usersService->verifyUserName($validData['name']);
            $validData['is_whatsapp'] = empty($validData['is_whatsapp']) ? 0 : 1;
            $updated = $this->usersService->updateById( $user->id, $validData);
            if ($validData['phone'] != $user->phone) {
                $this->clicksignService->replaceDocSigner($updated);
            }
            $response = [ 'status' => 'success', 'data' => new UserResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function uploadPicture( Request $request ) {
        try {
            
            $user = auth('api')->user();

            $userData = $request->validate([
                'picture' => 'required|image',
            ]);
            
            $updated = $this->usersService->uploadPicture($user, $userData['picture']);
            
            $response = [ 'status' => 'success', 'data' => new UserResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function inactivate( $id ) {
        try {
            

            $user = $this->usersService->find($id);

            if( $user->status == 'I' )
                throw ValidationException::withMessages(['Usuário já inativado']);
            
            $updated = $user->update(['status' => 'I']);
            
            $response = [ 'status' => 'success', 'data' => new UserResource($user) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function activate( $id ) {
        try {
            

            $user = $this->usersService->find($id);

            if( $user->status == 'A' )
                throw ValidationException::withMessages(['Usuário já ativo']);
            
            $updated = $user->update(['status' => 'A']);
            
            $response = [ 'status' => 'success', 'data' => new UserResource($user) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function getRegistrationFiles( $idUser ){

        try {

            $result = $this->registerFilesSerivce->list(['id_user' => $idUser]);

            $response = [ 'status' => 'success', 'data' => RegisterFileResource::collection($result) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }
}



