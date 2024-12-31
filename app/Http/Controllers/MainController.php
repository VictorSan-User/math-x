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

    public function generateExercises(Request $request): void
    {
        $request->validate([
            // sem espaços  
            'check_sum' => 'required_without_all:check_subtraction,check_multiplication,check_division',
            'check_subtraction' => 'required_without_all:check_sum,check_multiplication,check_division',
            'check_multiplication' => 'required_without_all:check_sum,check_subtraction,check_division',
            'check_division' => 'required_without_all:check_sum,check_subtraction,check_multiplication',
            'number_one' => 'required|integer|min:0|max:999|lt:number_two',
            'number_two' => 'required|integer|min:0|max:999',
            'number_exercises' => 'required|integer|min:5|max:50',
        ]);
        //buscar operações selecionadas
        $operations = [];
        $operations[] = $request->check_sum ? 'sum' : '';
        $operations[] = $request->ckeck_subtraction ? 'subtraction' : '';
        $operations[] = $request->check_multiplication ? 'multiplication' : '';
        $operations[] = $request->check_division ? 'division' : '';

        $min = $request->number_one;
        $max = $request->number_two;

        $numberExercises = $request->number_exercises;

        //gerar os exercicios randomicos
        // começa como uma coleção vazia
        $exercices = [];
        //conjunto de ciclos = numero de exercicios
        for ($index = 1; $index <= $numberExercises; $index++) {

            // definimos o tipo de operação conforme é preenchido a coleção acima
            $operation = $operations[array_rand($operations)];
            // numero 1 é randomico entre o min e max informados no formulario
            $number1 = rand($min, $max);
            // numero 2 a mesma coisa
            $number2 = rand($min, $max);

            $exercise = '';
            $solution = '';

            switch ($operation) {
                case 'sum':
                    $exercise = "$number1 + $number2 = ";
                    $solution = $number1 + $number2;
                    break;

                case 'subtraction':
                    $exercise = "$number1 - $number2 =";
                    $solution = $number1 - $number2;
                    break;
                case 'multiplication':
                    $exercise = "$number1 x $number2 =";
                    $solution = $number1 * $number2;
                    break;
                case 'division':
                    $exercise = "$number1 / $number2 =";
                    $solution = $number1 / $number2;
                    break;
            }

            $exercices[] = [
                'exercise_number' => $index,
                'exercise' => $exercise,
                'solution' => "$exercise $solution"
            ];
        }
        dd($exercices);
    }

    public function printExercises()
    {
        echo "Imprimir exercícios no navegador";
    }

    public function exportExercises()
    {
        echo "Exportar arquivos para um txt";
    }
}
