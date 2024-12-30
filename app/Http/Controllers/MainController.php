<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function home(){
        echo "Apresentar a pagina inicial";
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
