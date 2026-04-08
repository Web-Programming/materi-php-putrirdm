<?php
namespace Kendaraan;
//Cara penulisan class mobil
class Mobil{
    //cara penulisan property
    public $warna;
    public $merk;

    //cara penulisan method
    function maju() {
        // isi method maju()
        return "Mobil maju";
    }

    function berhenti() {
        // isi method berhenti()
        return "Mobil berhenti";
    }
}

//cara menggunakan namespace
//use kendaraan/mobil;
//membuat inisial namespace
use Kendaraan\Mobil as KMobil;

//instansiasi object dari namespace alias
$mobil_ahmad = new KMobil();

//instansiasi objek
//$mobil_ahmad = new Mobil();
$mobil_anton = new Mobil();

// set property
$mobil_ahmad->warna = "Hitam";
$mobil_ahmad->merk = "Toyota";

// tampilkan property
echo "Mobil Ahmad";
echo "<br>Warna : " . $mobil_ahmad->warna;
echo "<br>Merk : " . $mobil_ahmad->merk;

// tampilkan method
echo $mobil_ahmad->maju();
echo "<br>";
echo $mobil_ahmad->berhenti();
?>
