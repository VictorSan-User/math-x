<?php

namespace App\Http\Controllers;

use Illuminate\Cache\RedisTagSet;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use PhpParser\Node\Stmt\Echo_;

class MainController extends Controller
{
    public function home(): View
    {
        return view('home');
    }

    public function generateExercises(Request $request): View
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
        if ($request->check_sum) {
            $operations[] = 'sum';
        }
        if ($request->check_subtraction) {
            $operations[] = 'subtraction';
        }
        if ($request->check_multiplication) {
            $operations[] = 'multiplication';
        }
        if ($request->check_division) {
            $operations[] = 'division';
        }

        $min = $request->number_one;
        $max = $request->number_two;

        $numberExercises = $request->number_exercises;

        //gerar os exercicios randomicos
        // começa como uma coleção vazia
        $exercices = [];
        //conjunto de ciclos = numero de exercicios
        for ($index = 1; $index <= $numberExercises; $index++) {
            $exercises[] = $this->generateExercise($index, $operations, $min, $max);
        }
        //colocar os exercicios na sessao
        session(['exercises' => $exercises]);


        return view('operations', ['exercises' => $exercises]);
    }

    public function printExercises()
    {
        // VER SE EXISTEM NA SESSAO
        if(!session()->has('exercises')){
            return redirect()->route('home');
        }
        $exercises = session('exercises');

        echo '<pre>';
        echo '<h1>Exercícios de Matemática (' . env('APP_NAME') . ')</h1>';
        echo '<hr>';

        foreach($exercises as $exercise){
            echo '<h2><small>' . str_pad($exercise['exercise_number'], 2, '0',STR_PAD_LEFT) . '>> </small>' . $exercise['exercise'] . '</h2>' ;
        }

        //solucoes

        echo '<hr>';
        echo '<small>Soluções</small><br><br>';
        foreach($exercises as $exercise){
            echo '<small>' . str_pad($exercise['exercise_number'], 2, '0',STR_PAD_LEFT) . '>>' . $exercise['solution'] . '</small><br>' ;
        }

    }

    public function exportExercises()
    {
        // VER SE EXISTEM NA SESSAO
        if(!session()->has('exercises')){
            return redirect()->route('home');
        }
        $exercises = session('exercises');

        //criar o ficheiro para baixar em txt com os exercícios
        $filename = 'exercises_'. env('APP_NAME') . '_' . date('YmdHis') . '.txt';

        $content = '';
        $content = 'Exercícios de Matemática (' . env('APP_NAME') . ')' . "\n";
        foreach($exercises as $exercise){
            $content .= $exercise['exercise_number'] . '->' . $exercise['exercise']. "\n";
        }

        // solucao
        $content = "\n";
        $content = "Soluções\n" . str_repeat('-', 20). "\n";
        foreach($exercises as $exercise){
            $content .= $exercise['exercise_number']. '>' . $exercise['solution']. "\n";
        }

        return response($content)
                ->header('Content_Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="'. $filename. '"');
    }

    private function generateExercise($index, $operations, $min, $max): array
    {
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

                //evitar divisao por 0
                if ($number2 == 0) {
                    $number2 = 1;
                }

                $exercise = "$number1 : $number2 =";
                $solution = $number1 / $number2;
                break;
        }
        //se a solucao for float, arredondar pra 2 casas decimais
        if (is_float($solution)) {
            $solution = round($solution, 2);
        }

        return [
            'operation' => $operation,
            'exercise_number' => $index,
            'exercise' => $exercise,
            'solution' => "$exercise $solution"
        ];
    }
}
