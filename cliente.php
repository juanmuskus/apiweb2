<?php
$json_url = "http://localhost:8000/articulos/333";
$json = file_get_contents($json_url);
$data = json_decode($json, TRUE);
print_r($data);
echo "<br><hr><h2>id: ".$data['id']."</h2>";
echo "<h3>Descripción: ".$data['des']."</h3>";
echo "<h3>Cant.: ".$data['cant']."</h3>";
echo "<h3>Vr.: ".$data['vru']."</h3></div>";
?>
<style>
    body{
        margin:7rem;
        background: #eee;
        font-family: sans-serif;
        font-size: 1.5rem;
    }    
</style>