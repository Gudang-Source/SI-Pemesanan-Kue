<?php
include_once "../../library/inc.sesadmin.php";   // Validasi halaman harus Login
include_once "../../library/inc.connection.php"; // Membuka koneksi
include_once "../../library/inc.library.php";    // Membuka librari peringah fungsi
?>

<h2><font color="#FF0066">LAPORAN BARANG KELUAR USER</font></h2>

<form method="post" target="_self">
  <table border="1" class="table-list">
    <tr>
      <td colspan="4" bgcolor="#CCCCCC">
        <font color="#FF0066"><b>FILTER DATA </b></font>
      </td>
    </tr>
    <tr>
      <td width="55">
        <font color="#FF0066"><b>Periode </b></font>
      </td>
      <td width="5">
        <font color="#FF0066"><b>:</b></font>
      </td>
      <td>
        <input name="inptglawal" type="text" class="tcal" value="" size="20" /> s/d <input name="inptglakhir" type="text" class="tcal" value="" size="20" />
      </td>
      <td>
        <input name="tampiltgl" type="submit" value="Tampilkan" />
      </td>
    </tr>
    <tr>
      <td width="55">
        <font color="#FF0066"><b>Kategori</b></font>
      </td>
      <td width="5">
        <font color="#FF0066"><b>:</b></font>
      </td>
      <td>
        <select name="inpkategori" style="width: 100%">
          <option value="">- Pilih Kategori -</option>
          <?php
          $sql_k = "SELECT * FROM kategori";
          $qry_k = mysqli_query($mysqli, $sql_k) or die('MySQLI salah!'.mysqli_error($mysqli));
          while ($row = mysqli_fetch_array($qry_k, MYSQLI_ASSOC)) { ?>
          <option value="<?= $row['nm_kategori'] ?>"><?= $row['nm_kategori'] ?></option>
          <?php } ?>
        </select>
      </td>
      <td>
        <input name="tampilkategori" type="submit" value="Tampilkan" />
      </td>
    </tr>
  </table>
</form>

<?php if ( isset($_POST['tampiltgl']) ) { ?>
<font color="#FF0066">Daftar transaksi periode tanggal pesan </font><b><?= (isset($_POST['inptglawal'])) ? $_POST['inptglawal'] : '' ?></b><font color="#FF0066"> s/d </font><b><?= (isset($_POST['inptglakhir'])) ? $_POST['inptglakhir'] : '' ?></b><br /><br />
<?php } ?>


<div id="printtableArea">
  <table width="800" border="0" cellpadding="2" cellspacing="1" class="table-list">
  <tr>
    <th><font color="#FF0066">Nama Penerima</font></th>
    <th><font color="#FF0066">Nama Barang</font></th>
    <th><font color="#FF0066">Kategori</font></th>
    <th><font color="#FF0066">Stok Awal </font></th>
    <th><font color="#FF0066">Stok Tersedia</font></th>
    <th><font color="#FF0066">Stok Keluar</font></th>
    <th><font color="#FF0066">Tanggal Pemesanan</font></th>
    <th><font color="#FF0066">Gambar</font></th>
  </tr>
  <?php 

  if ( isset($_POST['tampiltgl']) ) {
    $tglawal  = InggrisTgl($_POST['inptglawal']);
    $tglakhir = InggrisTgl($_POST['inptglakhir']);
    $sql_1 = "SELECT  pemesanan.nama_penerima,  barang.nm_barang AS nama_barang,  barang.file_gambar AS gambar,  kategori.nm_kategori AS kategori,
    barang.stok + SUM(pemesanan_item.jumlah) AS stok_awal,  barang.stok AS stok_tersedia, SUM(pemesanan_item.jumlah) AS stok_keluar, SUM(pemesanan_item.harga) AS harga, SUM(pemesanan_item.jumlah) * SUM(pemesanan_item.harga) AS total, pemesanan.tgl_pemesanan  FROM pemesanan
    INNER JOIN pemesanan_item ON pemesanan.no_pemesanan = pemesanan_item.no_pemesanan INNER JOIN provinsi ON pemesanan.kd_provinsi = provinsi.kd_provinsi  INNER JOIN barang ON pemesanan_item.kd_barang = barang.kd_barang INNER JOIN kategori ON barang.kd_kategori = kategori.kd_kategori  WHERE pemesanan.tgl_pemesanan BETWEEN '$tglawal' AND '$tglakhir' GROUP BY    pemesanan.no_pemesanan";
  } else if ( isset($_POST['tampilkategori']) ) {
    $kategori = $_POST['inpkategori'];
    $sql_1 = "SELECT pemesanan.nama_penerima, barang.nm_barang AS nama_barang, barang.file_gambar AS gambar, kategori.nm_kategori AS kategori,
    barang.stok + SUM(pemesanan_item.jumlah) AS stok_awal, barang.stok AS stok_tersedia, SUM(pemesanan_item.jumlah) AS stok_keluar, SUM(pemesanan_item.harga) AS harga, SUM(pemesanan_item.jumlah) * SUM(pemesanan_item.harga) AS total, pemesanan.tgl_pemesanan FROM pemesanan  INNER JOIN pemesanan_item ON pemesanan.no_pemesanan = pemesanan_item.no_pemesanan INNER JOIN provinsi ON pemesanan.kd_provinsi = provinsi.kd_provinsi INNER JOIN barang ON pemesanan_item.kd_barang = barang.kd_barang INNER JOIN kategori ON barang.kd_kategori = kategori.kd_kategori 
    WHERE kategori.nm_kategori = '$kategori' GROUP BY pemesanan.no_pemesanan";
  } else {
    $sql_1 = "SELECT pemesanan.nama_penerima, barang.nm_barang AS nama_barang, barang.file_gambar AS gambar, kategori.nm_kategori AS kategori,
    barang.stok + SUM(pemesanan_item.jumlah) AS stok_awal, barang.stok AS stok_tersedia, SUM(pemesanan_item.jumlah) AS stok_keluar, SUM(pemesanan_item.harga) AS harga, SUM(pemesanan_item.jumlah) * SUM(pemesanan_item.harga) AS total, pemesanan.tgl_pemesanan FROM pemesanan
    INNER JOIN pemesanan_item ON pemesanan.no_pemesanan = pemesanan_item.no_pemesanan INNER JOIN provinsi ON pemesanan.kd_provinsi = provinsi.kd_provinsi INNER JOIN barang ON pemesanan_item.kd_barang = barang.kd_barang INNER JOIN kategori ON barang.kd_kategori = kategori.kd_kategori 
    GROUP BY pemesanan.no_pemesanan";
  }

  $qry_1 = mysqli_query($mysqli, $sql_1) or die ("Gagal query".mysqli_error($mysqli));
  $nomor = 1;
  $total = 0;
  while ($rows = mysqli_fetch_array($qry_1)) { ?>
  <?php $total += $rows['stok_keluar'] ?>
  <tr>
    <td align="center"><?= $rows['nama_penerima'] ?></td>
    <td align="center"><?= $rows['nama_barang'] ?></td>
    <td align="center"><?= $rows['kategori'] ?></td>
    <td align="center"><?= $rows['stok_awal'] ?></td>
    <td align="center"><?= $rows['stok_tersedia'] ?></td>
    <td align="center"><?= $rows['stok_keluar'] ?></td>
    <td align="center"><?= IndonesiaTgl($rows['tgl_pemesanan']) ?></td>
    <td align="center">
      <img src="../img-barang/<?= $rows['gambar'] ?>" width="100" height="100">
    </td>
  </tr>
  <?php } ?>
  <tr>
    <td colspan="5" align="center"><h3 style="margin: 0;">Jumlah</h3></td>
    <td align="center"><strong><?= $total ?></strong></td>
    <td colspan="3"></td>
  </tr>
</table>
</div>
<div>
<table>
<tr>
    <td width="700">
    </td>
    <td> 
      <button onclick="printDiv('printtableArea')" class="btn btn-danger">Cetak Laporan</button>
    </td>
  </tr>
  
</table>
</div>
<script> 
        function printDiv(divName) {
          var printContents = document.getElementById(divName).innerHTML;
          var originalContents = document.body.innerHTML;
          document.body.innerHTML = printContents;
          window.print();
          document.body.innerHTML = originalContents;
        }
</script>
