<?php

require_once ROOT_PLUGIN_PATH . 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;
class Spot_Lite_HTML_Exporter
{
    public $template = '<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório</title>
    <style>

        header {
            display: flex;
            flex-wrap: wrap;
            width: auto;  
            max-height: 200px;
        }

        #pictues_place{
            width: auto;
            display: flex;
            flex-wrap: wrap;
        }

        #pictues_place > img {
            flex: 1;
        }

        #divheader{
            max-height: 200px;
            padding-left: 10%;
            flex: 2;
            margin: auto;
            font-size: medium;
            color:navy
        }

        table, th, td, tr {
            border: 1px solid black;
            border-collapse: collapse;
        }

        table {
            margin: auto;
        }

        th{
            background-color: rgb(3, 3, 115);
            color: white;
            font-size: large;
            padding: 0 10px;
        }


        .tb_est{
            width: 30%;
            
        }

        .tb_idd {
            width: 20%;
        }

        .tb_reg{
            width: 50%;
        }

        .image-container {
            width: 500px;
            height: 500px;
            overflow: hidden; 
            display: inline-block;
        }
        .image-container img {
            width: 100%; 
            object-fit: cover; 
        }

        

        
    </style>
</head>
<body>
    <header>
        
        <img width="168px" src="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse1.mm.bing.net%2Fth%3Fid%3DOIP.ES9ksz3vCsOk3FLFbhKffQHaG0%26pid%3DApi&f=1&ipt=f49ffffea3db7ff84e0f8f1337bbc1db069742605b38e0bc4bf4a75abf9c1332&ipo=images" alt="Logo univali">


        <div id="divheader">
            <h3 >FUNDAÇÃO UNIVERSIDADE DO VALE DO ITAJAÍ <br>
            PROJETO DE EXTENSÃO LITE IS COOL
            </h3>
            <p class="data"></p>
        </div>
    </header>
    <main>
        <h1 style="text-align: center;">{{title}}</h1>  
         <h3>Relatório geral do encontro:</h3>
         <p id="relatorio_geral">{{description}}</p>
         <h3>Fotos:</h3>
         <div id="pictues_place">
            {{photos}}
         </div>

         <h3>Relatório individual:</h3>
         <table>
            <tr>
                <th class="tb_est">Estudante</th>
                <th class="tb_idd">Idade</th>
                <th class="tb_reg">Registro</th>
            </tr>
            {{activities}}
         </table>
    </main>

    <button onclick="print_as_pdf()">Imprimir</button>
   
    <script>
        function print_as_pdf(){
            const button = document.querySelector("button");
            button.style.display = "none";
            window.print();
        }
    </script>
</body>
</html>';

    public function mount($template, $title, $description, $photo_urls, $activities)
    {
        $photos = '';
        foreach ($photo_urls as $photo_url) {
            $photos .= '
            <div class="image-container">
                <img src="' . $photo_url . '" alt="Foto do encontro">
            </div>';
        }

        $activities_html = '';
        foreach ($activities as $activity) {
            $participant_name = $activity['participant_name'];
            $participant_age = $activity['participant_age'];
            $desc = $activity['description'];
            $activities_html .= "<tr>
      <td class='tb_est'>$participant_name</td>
      <td class='tb_idd'>$participant_age</td>
      <td class='tb_reg'>$desc</td>
    </tr>";
        }

        $html = str_replace('{{title}}', $title, $template);
        $html = str_replace('{{description}}', $description, $html);
        $html = str_replace('{{photos}}', $photos, $html);
        $html = str_replace('{{activities}}', $activities_html, $html);

        return $html;
    }
}

?>