<?php

class User
{

    protected $nama;
    protected $noHP;
    public function __construct($nama, $noHP)
    {

        if (empty($nama)) {
            echo "Nama tidak boleh kosong!<br>";
        }

        if (strlen($noHP) < 10) {
            echo "Nomor HP minimal 10 digit!<br>";
        }

        $this->nama = $nama;
        $this->noHP = $noHP;
    }

    public function getNama()
    {
        return $this->nama;
    }

    public function getStatus()
    {
        return "User";
    }
}
class Pelanggan extends User
{
    private $poin;

    public function __construct($nama, $noHP, $poin = 0)
    {
        parent::__construct($nama, $noHP);
        $this->poin = $poin;
    }
    public function getStatus()
    {
        return "Pelanggan";
    }
    public function tambahPoin($totalPembayaran)
    {
        $poinBaru = floor($totalPembayaran / 10000);
        $this->poin += $poinBaru;
    }
    public function getPoin()
    {
        return $this->poin;
    }
}

class Layanan
{
    private $jenisLayanan;
    private $tarif;

    public function __construct($jenisLayanan)
    {
        $this->jenisLayanan = $jenisLayanan;
        if ($jenisLayanan == "goRide Reguler") {
            $this->tarif = 2500;
        } elseif ($jenisLayanan == "goRide Prioritas") {
            $this->tarif = 3000;
        } elseif ($jenisLayanan == "goCar") {
            $this->tarif = 4500;
        } elseif ($jenisLayanan == "goCar XL") {
            $this->tarif = 6000;
        } elseif ($jenisLayanan == "goFood") {
            $this->tarif = 2000;
        } else {
            echo "Layanan tidak tersedia!<br>";
            $this->tarif = 0;
        }
    }
    public function getTarif()
    {
        return $this->tarif;
    }

    public function getJenisLayanan()
    {
        return $this->jenisLayanan;
    }
}

class Voucher
{

    private $kodeVoucher;
    private $diskonPersen;

    public function __construct($kodeVoucher)
    {

        $this->kodeVoucher = $kodeVoucher;

        if ($kodeVoucher == "HEMAT10") {
            $this->diskonPersen = 0.10;
        } elseif ($kodeVoucher == "HEMAT20") {
            $this->diskonPersen = 0.20;
        } elseif ($kodeVoucher == "HEMAT30") {
            $this->diskonPersen = 0.30;
        } else {
            echo "Voucher tidak ada!<br>";
            $this->diskonPersen;
        }
    }

    public function hitungDiskon($subtotal)
    {
        return $subtotal * $this->diskonPersen;
    }
}

abstract class Pembayaran
{

    abstract public function getMetode();
    abstract public function biayaAdmin();
}

class Ewallet extends Pembayaran
{

    public function getMetode()
    {
        return "E-Wallet";
    }

    public function biayaAdmin()
    {
        return 1000;
    }
}

class Transferbank extends Pembayaran
{

    public function getMetode()
    {
        return "Transfer Bank";
    }

    public function biayaAdmin()
    {
        return 2500;
    }
}

class Cash extends Pembayaran
{

    public function getMetode()
    {
        return "Cash";
    }

    public function biayaAdmin()
    {
        return 0;
    }
}

class Transaksi
{

    private $pelanggan;
    private $layanan;
    private $pembayaran;
    private $voucher;
    private $jarakTempuh;

    private static $totalTransaksi = 0;

    public function __construct($pelanggan, $layanan, $pembayaran, $jarakTempuh, $voucher)
    {

        if ($jarakTempuh <= 0) {
            echo "Jarak harus lebih dari 0!<br>";
        }

        $this->pelanggan = $pelanggan;
        $this->layanan = $layanan;
        $this->pembayaran = $pembayaran;
        $this->voucher = $voucher;
        $this->jarakTempuh = $jarakTempuh;

        self::$totalTransaksi++;
    }

    public function hitungSubtotal()
    {
        return $this->jarakTempuh * $this->layanan->getTarif();
    }

    public function hitungDiskonMember()
    {

        $subtotal = $this->hitungSubtotal();

        if ($subtotal > 50000) {
            return $subtotal * 0.05;
        }

        return 0;
    }

    public function hitungDiskonVoucher()
    {

        if ($this->voucher != null) {
            return $this->voucher->hitungDiskon(
                $this->hitungSubtotal()
            );
        }

        return 0;
    }

    public function hitungBiayaAdmin()
    {
        return $this->pembayaran->biayaAdmin();
    }

    public function hitungTotal()
    {

        return $this->hitungSubtotal() - $this->hitungDiskonMember() - $this->hitungDiskonVoucher() + $this->hitungBiayaAdmin();
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ojek Online</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 400px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            text-align: left;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            margin-top: 5px;
        }

        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            margin-top: 5px;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #28a745;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #000000;
        }

        hr {
            margin: 20px 0;
        }
    </style>
</head>

<body>


    <div class="container">

        <h2>Sistem Ojek Online</h2>

        <form method="POST">

            <label>Nama</label><br>
            <input type="text" name="nama">
            <br>

            <label>No HP</label><br>
            <input type="text" name="noHP" required>
            <br><br>

            <label>jarak Tempuh (KM)</label><br>
            <input type="number" name="jarak" required>
            <br><br>

            <label>Jenis Layanan</label><br>
            <select name="layanan">
                <option value="goRide Reguler">goRide Reguler</option>
                <option value="goRide Prioritas">goRide Prioritas</option>
                <option value="goCar">goCar</option>
                <option value="goCar XL">goCar XL</option>
                <option value="goFood">goFood</option>
            </select>
            <br><br>

            <label>Voucher</label><br>
            <input type="text" name="voucher">
            <br><br>

            <label>Metode Pembayaran</label><br>
            <select name="pembayaran">
                <option value="ewallet">E-Wallet</option>
                <option value="transfer">Transfer Bank</option>
                <option value="cash">Cash</option>
            </select>
            <br><br>

            <button type="submit" name="pesan">Pesan</button>

        </form>

        <hr>

        <?php

        if (isset($_POST['pesan'])) {

            $nama = $_POST['nama'];
            $noHP = $_POST['noHP'];
            $jarak = $_POST['jarak'];
            $layanan = $_POST['layanan'];
            $voucher = $_POST['voucher'];
            $metodeBayar = $_POST['pembayaran'];

            $poin = 0;

            $pelanggan = new Pelanggan($nama, $noHP, $poin);

            $layanan1 = new Layanan($layanan);

            $voucher1 = null;

            if (!empty($voucher)) {
                $voucher1 = new Voucher($voucher);
            }

            if ($metodeBayar == "ewallet") {
                $pembayaran = new Ewallet();
            } elseif ($metodeBayar == "transfer") {
                $pembayaran = new Transferbank();
            } else {
                $pembayaran = new Cash();
            }

            $transaksi = new Transaksi(
                $pelanggan,
                $layanan1,
                $pembayaran,
                $jarak,
                $voucher1
            );

            $pelanggan->tambahPoin($transaksi->hitungTotal());

            echo "<h3>Hasil Transaksi</h3>";
            echo "Nama: " . $pelanggan->getNama() . "<br>";
            echo "Status: " . $pelanggan->getStatus() . "<br>";
            echo "Layanan: " . $layanan1->getJenisLayanan() . "<br>";
            echo "Jarak :" . $jarak . " KM<br>";
            echo "Metode Pembayaran: " . $pembayaran->getMetode() . "<br>";
            echo "Subtotal: Rp " . number_format($transaksi->hitungSubtotal(),  0, ',', '.') . "<br>";
            echo "Diskon Member: Rp " . number_format($transaksi->hitungDiskonMember(), 0,  ',', '.') . "<br>";
            echo "Diskon Voucher: Rp " . number_format($transaksi->hitungDiskonVoucher(), 0,  ',',  '.') . "<br>";
            echo "Biaya Admin: Rp " . number_format($transaksi->hitungBiayaAdmin(), 0,  ',',  '.') . "<br>";
            echo "<b>Total Bayar: Rp " . number_format($transaksi->hitungTotal(), 0,  ',',  '.') . "</b><br>";
            echo "Poin Sekarang: " . $pelanggan->getPoin();
        }

        ?>
    </div>
</body>

</html>