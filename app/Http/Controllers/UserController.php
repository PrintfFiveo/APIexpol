<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Sanctum;

class UserController extends Controller
{
    
    public function index()
    {
        return User::all();
    }

  
    public function store(Request $request)
    {
        try {  $valid = $request -> validate([
            'name' => 'max:35|required',
            'email' => 'max:40|required',
            'password' => 'max:20|required',
            'turma_id'=> ''
            
    ]);}
   
    catch(ValidationException $e){
        return Response::json(['error' => $e]);
    }
    $register = User::create($valid);
  
    return Response::json(['register' => $register ]);

    }

   
    public function show($id)
    {
        $turma = User::find($id);
        if (!$turma) {
            return response()->json([
                'message' => 'turma não encontrada.'
            ], 404);
        }

        $turmas2 = DB::table('users')
        ->join('turmas', 'users.turma_id', '=', 'turmas.id')
        ->where('users.id', 'LIKE', '%' . $id . '%')
        ->select('turmas.*')  
        ->get();

        return response()->json([
            'message' => 'Detalhes da turma.',
            'data' => $turma


        ]);  
         
    }
    

    
    public function update(Request $request, $id)
    {
       
        $turma = User::findOrFail($id);
         $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'turma_id'=> 'required'
         
         ]);
         

        

    
        $turma->fill($validatedData);
        $turma->save();


        return Response::json([
            'message' => 'user atualizado com sucesso.',
            'data' => $turma
        ]);
    }


   
     
    public function patch(Request $request, $id)
    {
        $validatedData = $request->validate([
            'turma_id' => 'sometimes|integer',
        ]);

      
        $turma = User::findOrFail($id);

       
        $turma->update($validatedData);

        return response()->json([
            'message' => 'Turma atualizada com sucesso.',
            'data' => $turma
        ]);
   }


     
    public function destroy(string $id)
    {
        $turma = User::find($id);

        if (!$turma) {
            return response()->json([
                'message' => 'User não encontrado.'
            ], 404);
        }

        $turma->delete();

        return response()->json([
            'message' => 'User deletado com sucesso.'
        ]);

    }
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($data)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            'message' => 'Login bem-sucedido.',
            'token' => $token,
            'user' => $user
        ]);
    }
      public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.'
        ]);
    }
}
