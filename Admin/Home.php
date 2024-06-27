<?php
include 'config/db.php';

// Mendapatkan informasi sekolah
$oke = mysqli_query($con, "SELECT * FROM tb_sekolah WHERE id_sekolah='1'");
$oke1 = mysqli_fetch_array($oke);

// Fungsi untuk mendapatkan gambar dari BLOB
function getImageFromBlob($blob) {
    return 'data:image/png;base64,' . base64_encode($blob);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel e-Learning</title>
    <link rel="stylesheet" href="path/to/your/css/file.css"> <!-- Sesuaikan dengan path file CSS Anda -->
</head>

<body>
    <div class="content-wrapper">
        <h3><b>Dashboard</b> <small class="text-muted">/Admin</small></h3>
        <hr>
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <center>
                            <h2>
                                PANEL ADMIN APLIKASI <strong>e-learning</strong><br>
                                <?php echo $oke1['nama_sekolah']; ?>
                            </h2>
                        </center>
                        <?php
                        $faces = mysqli_query($con, "SELECT ta.nama_lengkap, tfi.face_image, ts.nama_siswa, tg.nama_guru, tfi.created_at 
                            FROM `tb_face_images` tfi
                            LEFT JOIN tb_admin ta ON tfi.user_id = ta.id_admin
                            LEFT JOIN tb_siswa ts ON tfi.user_id = ts.id_siswa
                            LEFT JOIN tb_guru tg ON tfi.user_id = tg.id_guru");
                        ?>
                        <table class="table table-condensed table-striped table-hover" id="data">
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>User</th>
                                    <th>Login</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($f = mysqli_fetch_assoc($faces)) {
                                    // Tentukan nama pengguna berdasarkan level
                                    $user = !empty($f['nama_lengkap']) ? $f['nama_lengkap'] : (!empty($f['nama_siswa']) ? $f['nama_siswa'] : (!empty($f['nama_guru']) ? $f['nama_guru'] : 'Unknown User'));
                                ?>
                                    <tr>
                                        <td><img src="<?= $f['face_image']; ?>" class="img-face" style="width:60px;height:60px;"></td>
                                        <!-- <td><textarea name="" id=""><?= $f['face_image']; ?></textarea></td> -->
                                        <td><?= htmlspecialchars($user) ?></td>
                                        <td><?= htmlspecialchars($f['created_at']) ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-3" style="overflow:scroll;height:600px;border-radius:7px;background-color:white">
                <p class="mt-3 ml-3">
                    <h4><i class="fa fa-user text-success"></i> <b class="text-success">User</b> Konfirmasi</h4>
                    <hr>
                </p>
                <div class="mt-3">
                    <!-- info daftar guru -->
                    <?php
                    $sqlg = mysqli_query($con, "SELECT * FROM tb_guru WHERE status='N' ORDER BY id_guru DESC");
                    ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nama Guru</th>
                                <th>Konfirmasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($d = mysqli_fetch_assoc($sqlg)) {
                                if ($d['confirm'] != 'Yes') { ?>
                                    <tr>
                                        <td><img src="<?= getImageFromBlob($d['foto']); ?>" class="img-thumbnail" style="width:60px;height:60px;"></td>
                                        <td><?= $d['nama_guru']; ?></td>
                                        <td><a href="" data-toggle="modal" data-target="#guru<?= $d['id_guru'] ?>">Konfirmasi</a></td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>

                    <!-- Modal untuk setiap guru -->
                    <?php while ($d = mysqli_fetch_assoc($sqlg)) {
                        if ($d['confirm'] != 'Yes') { ?>
                            <div class="modal fade" id="guru<?= $d['id_guru'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title"> Informasi Pendaftar </h4>
                                        </div>
                                        <center>
                                            <h4>GURU</h4>
                                        </center>
                                        <table class="table">
                                            <tr>
                                                <td colspan="3" align="center">
                                                    <img src="<?= getImageFromBlob($d['foto']); ?>" class="img-thumbnail" style="width:60px;height:60px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Nip/Nuptk</td>
                                                <td>:</td>
                                                <td><?= $d['nik']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Nama Guru</td>
                                                <td>:</td>
                                                <td><?= $d['nama_guru']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td>:</td>
                                                <td><?= $d['email']; ?></td>
                                            </tr>
                                        </table>
                                        <div class="modal-footer">
                                            <a href="?page=guru&act=unconfirm&id=<?= $d['id_guru']; ?>" class="btn btn-danger">Tolak</a>
                                            <a href="?page=guru&act=confirm&id=<?= $d['id_guru']; ?>" class="btn btn-success"> Setujui</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php }
                    } ?>

                    <!-- info daftar siswa -->
                    <?php
                    $sqls = mysqli_query($con, "SELECT * FROM tb_siswa
                        INNER JOIN tb_master_kelas ON tb_siswa.id_kelas=tb_master_kelas.id_kelas
                        INNER JOIN tb_master_jurusan ON tb_siswa.id_jurusan=tb_master_jurusan.id_jurusan
                        WHERE tb_siswa.aktif='N' ORDER BY tb_siswa.id_siswa DESC");
                    ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nama Siswa</th>
                                <th>Konfirmasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($d = mysqli_fetch_assoc($sqls)) {
                                if ($d['confirm'] != 'Yes') { ?>
                                    <tr>
                                        <td><img src="<?= getImageFromBlob($d['foto']); ?>" class="img-thumbnail" style="width:60px;height:60px;"></td>
                                        <td><?= $d['nama_siswa']; ?></td>
                                        <td><a href="" data-toggle="modal" data-target="#siswa<?= $d['id_siswa'] ?>">Konfirmasi</a></td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>

                    <!-- Modal untuk setiap siswa -->
                    <?php while ($d = mysqli_fetch_assoc($sqls)) {
                        if ($d['confirm'] != 'Yes') { ?>
                            <div class="modal fade" id="siswa<?= $d['id_siswa'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title"> Informasi Pendaftar </h4>
                                        </div>
                                        <center>
                                            <h4>SISWA</h4>
                                        </center>
                                        <table class="table">
                                            <tr>
                                                <td colspan="3" align="center">
                                                    <img src="<?= getImageFromBlob($d['foto']); ?>" class="img-thumbnail" style="width:60px;height:60px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Nis</td>
                                                <td>:</td>
                                                <td><?= $d['nis']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Nama Siswa</td>
                                                <td>:</td>
                                                <td><?= $d['nama_siswa']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Kelas/Jurusan</td>
                                                <td>:</td>
                                                <td><?= $d['kelas']; ?>/<?= $d['jurusan']; ?></td>
                                            </tr>
                                        </table>
                                        <div class="modal-footer">
                                            <a href="?page=siswa&act=unconfirm&id=<?= $d['id_siswa']; ?>" class="btn btn-danger">Tolak</a>
                                            <a href="?page=siswa&act=confirm&id=<?= $d['id_siswa']; ?>" class="btn btn-success"> Setujui</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php }
                    } ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
