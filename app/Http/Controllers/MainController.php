<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function home(): View
    {
        return view('home');
    }

    public function generateExercises(Request $request){
        echo "Gerando os exercícios";
    }
    
    public function printExercises(){
        echo "Imprimir exercícios no navegador";
    }

    public function exportExercises(){
        echo "Exportar arquivos para um txt";
    }
}
