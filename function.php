<?php
    session_start();
    
    //membuat koneksi ke database
    $conn = mysqli_connect("localhost","root","","stokbarang");

    //menambah barang baru
if(isset($_POST['addnewbarang'])){
    $namabarang  = $_POST['namabarang'];
    $deskripsi  = $_POST['deskripsi'];
    $stock      = $_POST['stock'];
    

    $addtotable = mysqli_query($conn,"insert into stock(namabarang, deskripsi, stock)values('$namabarang','$deskripsi','$stock')");
    if($addtotable){
        echo 'berhasil';
        header('location:index.php');

    }else{
        echo'gagal';
        header('location:index.php');
    }
};  


    //menambah barang masuk
    if (isset($_POST['barangmasuk'])) {
        $barangnya = $_POST['barangnya'];
        $penerima = $_POST['penerima'];
        $qty = $_POST['qty'];
        

        $cekstocksekarang = mysqli_query($conn,"select * from stock where idbarang='$barangnya'");
        $ambildatanya = mysqli_fetch_array($cekstocksekarang);

        $stocksekarang = $ambildatanya['stock'];
        $tambahkanstocksekarangdenganquantity = $stocksekarang+$qty;

        $addtomasuk = mysqli_query($conn, "insert into masuk (idbarang, keterangan, qty) values('$barangnya','$penerima','$qty')");
        $updatestockmasuk = mysqli_query($conn, "update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
        if ($addtomasuk&&$updatestockmasuk) {
            header('location:masuk.php');
        }else{
            echo 'gagal';
            header('location:masuk.php');
        }
    };

    //menambah barang keluar
    if (isset($_POST['barangkeluar'])) {
        $barangnya = $_POST['barangnya'];
        $penerima = $_POST['penerima'];
        $qty = $_POST['qty'];
        

        $cekstocksekarang = mysqli_query($conn,"select * from stock where idbarang='$barangnya'");
        $ambildatanya = mysqli_fetch_array($cekstocksekarang);

        $stocksekarang = $ambildatanya['stock'];

        if ($stocksekarang >= $qty) {
            //kalau barangnya cukup
        $tambahkanstocksekarangdenganquantity = $stocksekarang-$qty;

        $addtokeluar = mysqli_query($conn, "insert into keluar (idbarang, penerima, qty) values('$barangnya','$penerima','$qty')");
        $updatestockmasuk = mysqli_query($conn, "update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
        if ($addtokeluar&&$updatestockmasuk) {
            header('location:keluar.php');
        }else{
            echo 'gagal';
            header('location:keluar.php');
        }

        }else{
            //kalau barangnya ga cukup
            echo '
            <script>
                alert("Stock saat ini tidak mencukupi");
                window.location.href="keluar.php";
                
            </script>
            ';
        }
    }


    //update info barang
    if (isset($_POST['updatebarang'])) {
        $idb = $_POST['idb'];
        $namabarang = $_POST['namabarang'];
        $deskripsi = $_POST['deskripsi'];

        $update = mysqli_query($conn, "update stock set namabarang='$namabarang', deskripsi='$deskripsi' where idbarang = '$idb'");
        if ($update) {
            header('location:index.php');
        }else{
            echo 'gagal';
            header('location:index.php');
        }
    }


    //menghapus barang  dari stock
    if (isset($_POST['hapusbarang'])) {
        $idb = $_POST['idb'];
        

        $hapus = mysqli_query($conn, "delete from stock where idbarang = '$idb'");
        if ($hapus) {
            header('location:index.php');
        }else{
            echo 'gagal';
            header('location:index.php');
        }
    };


    //mengubah data barang masuk
    if(isset($_POST['updatebarangmasuk'])){
        $idb = $_POST['idb'];
        $idm = $_POST['idm'];
        $deskripsi = $_POST['keterangan'];
        $qty = $_POST['qty'];

        $lihatstock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
        $stocknya = mysqli_fetch_array($lihatstock);
        $stockskrg = $stocknya['stock'];

        $qtyskrg = mysqli_query($conn, "select * from masuk where idmasuk='$idm'");
        $qtynya = mysqli_fetch_array($qtyskrg);
        $qtyskrg = $qtynya['qty'];

        if ($qty>$qtyskrg) {
            $selisih = $qty-$qtyskrg;
            $kurangin = $stockskrg + $selisih;
            $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
            $updatenya = mysqli_query($conn,"update masuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
                if ($kurangistocknya&&$updatenya) {
                    header('location:masuk.php');
                    } else {
                        echo 'Gagal';
                        header('location:masuk.php');
                }
        } else {
            $selisih = $qtyskrg - $qty;
            $kurangin = $stockskrg - $selisih;
            $kurangistocknya = mysqli_query($conn, "update stock set stock = '$kurangin' where idbarang='$idb'");
            $updatenya = mysqli_query($conn,"update masuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
                if ($kurangistocknya&&$updatenya) {
                    header('location:masuk.php');
                    } else {
                        echo 'Gagal';
                        header('location:masuk.php');
                }
        }
    }

    //menghapus barang masuk
        if(isset($_POST['hapusbarangmasuk'])){
            $idb = $_POST['idb'];
            $qty = $_POST['kty'];
            $idm = $_POST['idm'];

            $getdatastock = mysqli_query($conn,"select * from stock where idbarang ='$idb'");
            $data = mysqli_fetch_array($getdatastock);
            $stock = $data['stock'];

            $selisih = $stock - $qty;

            $update = mysqli_query($conn,"update stock set stock ='$selisih' where idbarang='$idb'");
            $hapusdata = mysqli_query($conn,"delete from masuk where idmasuk='$idm'");


            if($update&&$hapusdata){
                header('location:masuk.php');
            }else{
                header('location:masuk.php');
            }
        }

    
        //mengubah data barang keluar
        if(isset($_POST['updatebarangkeluar'])){
            $idb = $_POST['idb'];
            $idk = $_POST['idk'];
            $penerima = $_POST['penerima'];
            $qty = $_POST['qty'];
    
            $lihatstock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
            $stocknya = mysqli_fetch_array($lihatstock);
            $stockskrg = $stocknya['stock'];
    
            $qtyskrg = mysqli_query($conn, "select * from keluar where idkeluar='$idk'");
            $qtynya = mysqli_fetch_array($qtyskrg);
            $qtyskrg = $qtynya['qty'];
    
            if ($qty>$qtyskrg) {
                $selisih = $qty-$qtyskrg;
                $kurangin = $stockskrg - $selisih;
                $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
                $updatenya = mysqli_query($conn,"update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
                    if ($kurangistocknya&&$updatenya) {
                        header('location:keluar.php');
                        } else {
                            echo 'Gagal';
                            header('location:keluar.php');
                    }
            } else {
                $selisih = $qtyskrg - $qty;
                $kurangin = $stockskrg + $selisih;
                $kurangistocknya = mysqli_query($conn, "update stock set stock = '$kurangin' where idbarang='$idb'");
                $updatenya = mysqli_query($conn,"update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
                    if ($kurangistocknya&&$updatenya) {
                        header('location:keluar.php');
                        } else {
                            echo 'Gagal';
                            header('location:keluar.php');
                    }
            }
        }


        //menghapus barang keluar
        if(isset($_POST['hapusbarangkeluar'])){
            $idb = $_POST['idb'];
            $qty = $_POST['kty'];
            $idk = $_POST['idk'];

            $getdatastock = mysqli_query($conn,"select * from stock where idbarang ='$idb'");
            $data = mysqli_fetch_array($getdatastock);
            $stock = $data['stock'];

            $selisih = $stock + $qty;

            $update = mysqli_query($conn,"update stock set stock ='$selisih' where idbarang='$idb'");
            $hapusdata = mysqli_query($conn,"delete from keluar where idkeluar='$idk'");


            if($update&&$hapusdata){
                header('location:keluar.php');
            }else{
                header('location:keluar.php');
            }
        }



        //menambah admin baru
        if (isset($_POST['addadmin'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $queryinsert = mysqli_query($conn, "insert into login(email, password) values ('$email','$password')");

            if ($queryinsert) {
                header('location:admin.php');
            }else{
                header('location:admin.php');
            }
        }


        //edit data admin
        if (isset($_POST['updateadmin'])) {
            $emailbaru = $_POST['emailadmin'];
            $passwordbaru = $_POST['passwordbaru'];
            $idnya = $_POST['id'];

            $queryupdate = mysqli_query($conn,"update login set email='$emailbaru', password='$passwordbaru' where iduser='$idnya'");

            if ($queryupdate) {
                header('location:admin.php');
            }else{
                header('location:admin.php');
            }
        }


        //hapus data admin
        if (isset($_POST['hapusadmin'])) {
            $id = $_POST['id'];

            $querydelete = mysqli_query($conn, "delete from login where iduser='$id'");
            if ($querydelete) {
                header('location:admin.php');
            }else{
                header('location:admin.php');
            }
        }


        //update tanggal indonesia
        if (isset($_POST['updatebarangmasuk'])) {
            $tanggal = $_POST['tanggal'];
        
            date_default_timezone_set("Asia/Jakarta");
            $tanggal = date('N j/n/Y H:i');
            function tanggal_indonesia($tanggal){
                $nama_hari = array(1=>'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Ahad');
                $nama_bulan = array(1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','Nopember','Desember');

                $pisah_waktu = explode(" ",$tanggal);
                $hari = $pisah_waktu[0];
                $tgl = $pisah_waktu[1];
                $jam = $pisah_waktu[2];

                $hari_baru = $nama_hari[$hari];
                $pisah_tanggal = explode ("/",$tgl);
                $tanggal_baru = $pisah_tanggal[0]." ".$nama_bulan[$pisah_tanggal[1]]." ".$pisah_tanggal[2];

                return $hari_baru.", ".$tanggal_baru." pukul ".$jam." WIB";
            }

            echo tanggal_indonesia($waktu_lengkap);
    
    }


?>
