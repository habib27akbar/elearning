<?php
session_start();
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>E-Learning</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="vendor/images/favicon.png" />
    <link rel="stylesheet" type="text/css" href="vendor/login/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/login/fonts/iconic/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/login/vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="vendor/login/vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/login/vendor/animsition/css/animsition.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/login/vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/login/vendor/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="vendor/login/css/util.css">
    <link rel="stylesheet" type="text/css" href="vendor/login/css/main.css">
    <link rel="stylesheet" href="vendor/node_modules/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendor/node_modules/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendor/css/style.css">
    <link href="vendor/sweetalert/sweetalert.css" rel="stylesheet" />

    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/blazeface"></script>
</head>

<body>

    <div class="limiter">
        <div class="container-login100" style="background-image: url('vendor/login/images/bg-01.jpg');">
            <div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
                <center><img src="vendor/images/Logo-Cover.png" alt="" height="120" width="110"></center>
                <div style="position: fixed; top: -100px">
                    <video id="video" width="80" height="68" autoplay></video>
                    <canvas id="canvas" width="80" height="68"></canvas>
                </div>
                <form method="post" action="" class="login100-form validate-form">
                    <span class="login100-form-title p-b-49">
                        E-LEARNING LOGIN
                    </span>

                    <div class="wrap-input100 validate-input m-b-23" data-validate="Username is required">
                        <span class="label-input100">Username</span>
                        <input class="input100" type="text" name="username" placeholder="Type your username">
                        <span class="focus-input100" data-symbol="&#xf206;"></span>
                    </div>

                    <div class="wrap-input100 validate-input m-b-23" data-validate="Password is required">
                        <span class="label-input100">Password</span>
                        <input class="input100" type="password" name="password" placeholder="Type your password">
                        <span class="focus-input100" data-symbol="&#xf190;"></span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate="User type is required">
                        <span class="label-input100">User</span>
                        <select name="level" class="form-control" required style="background-color: #212121; border-radius: 7px; color: #fff; font-weight: bold;">
                            <option value="">-- Pilih Level --</option>
                            <option value="1">Guru</option>
                            <option value="2">Siswa</option>
                            <option value="3">Admin</option>
                        </select>
                    </div>

                    <div class="text-right p-t-8 p-b-31">
                        <a href="https://wa.me/6282311801697">
                            Forgot password?
                        </a>
                    </div>

                    <div class="container-login100-form-btn m-b-23">
                        <div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button value="LOGIN" name="Login" type="submit" class="login100-form-btn">
                                Login
                            </button>
                        </div>
                    </div>

                    <input type="hidden" id="face_image" name="face_image">
                </form>

                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $email = trim(mysqli_real_escape_string($con, $_POST['username']));
                    $pass = sha1($_POST['password']);
                    $level = $_POST['level'];
                    $face_image = $_POST['face_image'];

                    if ($level == '1') {
                        $sql = mysqli_query($con, "SELECT * FROM tb_guru WHERE email='$email' AND password='$pass' AND status='Y'");
                        $data = mysqli_fetch_array($sql);
                        $id = $data[0];
                        $cek = mysqli_num_rows($sql);

                        if ($cek > 0) {
                            $_SESSION['Guru'] = $id;
                            $_SESSION['upload_gambar'] = TRUE;

                            // Insert face image ke dalam tabel tb_face_images
                            $insert_stmt = $con->prepare("INSERT INTO tb_face_images (face_image, level, user_id) VALUES (?, ?, ?)");
                            $insert_stmt->bind_param("sii", $face_image, $level, $id);
                            $insert_stmt->execute();
                            $insert_stmt->close();

                            echo "<script>
                                Swal.fire({
                                    title: 'Sukses',
                                    text: 'Login Berhasil..',
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: true
                                }).then(() => {
                                    window.location.replace('Guru/index.php');
                                });
                            </script>";
                        } else {
                            echo "<script>
                                Swal.fire({
                                    title: 'Error',
                                    text: 'User ID / Password Salah Atau Belum Dikonfirmasi Oleh Admin!',
                                    icon: 'error',
                                    timer: 3000,
                                    showConfirmButton: true
                                }).then(() => {
                                    window.location.replace('?pages=login');
                                });
                            </script>";
                        }
                    } elseif ($level == '2') {
                        $sql = mysqli_query($con, "SELECT * FROM tb_siswa WHERE nis='$email' AND password='$pass' AND aktif='Y'");
                        $data = mysqli_fetch_array($sql);
                        $id = $data[0];
                        $cek = mysqli_num_rows($sql);

                        if ($cek > 0) {
                            $_SESSION['Siswa'] = $id;
                            $_SESSION['username'] = $data['nis'];
                            $_SESSION['namalengkap'] = $data['nama_siswa'];
                            $_SESSION['password'] = $data['password'];
                            $_SESSION['nis'] = $data['nis'];
                            $_SESSION['id_siswa'] = $data['id_siswa'];
                            $_SESSION['kelas'] = $data['id_kelas'];
                            $_SESSION['jurusan'] = $data['id_jurusan'];
                            $_SESSION['tingkat'] = $data['tingkat'];
                            mysqli_query($con, "UPDATE tb_siswa SET status='Online' WHERE id_siswa='$data[id_siswa]'");

                            // Insert face image ke dalam tabel tb_face_images
                            $insert_stmt = $con->prepare("INSERT INTO tb_face_images (face_image, level, user_id) VALUES (?, ?, ?)");
                            $insert_stmt->bind_param("sii", $face_image, $level, $id);
                            $insert_stmt->execute();
                            $insert_stmt->close();

                            echo "<script>
                                Swal.fire({
                                    title: 'Sukses',
                                    text: 'Login Berhasil..',
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: true
                                }).then(() => {
                                    window.location.replace('Siswa/index.php');
                                });
                            </script>";
                        } else {
                            echo "<script>
                                Swal.fire({
                                    title: 'Error',
                                    text: 'User ID / Password Salah!',
                                    icon: 'error',
                                    timer: 3000,
                                    showConfirmButton: true
                                }).then(() => {
                                    window.location.replace('?pages=login');
                                });
                            </script>";
                        }
                    } elseif ($level == '3') {
                        $sql = mysqli_query($con, "SELECT * FROM tb_admin WHERE username='$email' AND password='$pass'");
                        $data = mysqli_fetch_array($sql);
                        $id = $data[0];
                        $cek = mysqli_num_rows($sql);

                        if ($cek > 0) {
                            $_SESSION['Admin'] = $id;

                            // Update face image
                            // Insert face image ke dalam tabel tb_face_images
                            $insert_stmt = $con->prepare("INSERT INTO tb_face_images (face_image, level, user_id) VALUES (?, ?, ?)");
                            $insert_stmt->bind_param("sii", $face_image, $level, $id);
                            $insert_stmt->execute();
                            $insert_stmt->close();

                            echo "<script>
                                Swal.fire({
                                    title: 'Sukses',
                                    text: 'Login Berhasil..',
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: true
                                }).then(() => {
                                    window.location.replace('Admin/index.php');
                                });
                            </script>";
                        } else {
                            echo "<script>
                                Swal.fire({
                                    title: 'Error',
                                    text: 'User ID / Password Salah!',
                                    icon: 'error',
                                    timer: 3000,
                                    showConfirmButton: true
                                }).then(() => {
                                    window.location.replace('?pages=login');
                                });
                            </script>";
                        }
                    }
                }
                ?>

            </div>
        </div>
    </div>

    <script src="vendor/login/vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="vendor/login/vendor/animsition/js/animsition.min.js"></script>
    <script src="vendor/login/vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/login/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/login/vendor/select2/select2.min.js"></script>
    <script src="vendor/login/vendor/daterangepicker/moment.min.js"></script>
    <script src="vendor/login/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="vendor/login/vendor/countdowntime/countdowntime.js"></script>
    <script src="vendor/login/js/main.js"></script>

    <script>
        async function detectFace() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');

            const model = await blazeface.load();
            let faceDetected = false;

            async function detect() {
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                const faces = await model.estimateFaces(canvas, false);

                if (faces.length > 0) {
                    console.log('Face detected');
                    const face_image = canvas.toDataURL('image/png');
                    document.getElementById('face_image').value = face_image;
                    faceDetected = true;
                } else {
                    faceDetected = false;
                }

                requestAnimationFrame(detect);
            }

            detect();

            // Check for face detection after a certain timeout
            setTimeout(() => {
                if (!faceDetected) {
                    Swal.fire({
                        title: 'Muka tidak terdeteksi',
                        text: 'Pastikan tidak ada object yang menutupi muka',
                        icon: 'warning',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            }, 5000); // Adjust timeout duration as needed
        }

        detectFace();

        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: "user"
            },
            audio: false
        }).then(stream => {
            const video = document.getElementById('video');
            video.srcObject = stream;
        }).catch(err => {
            console.error("Error accessing webcam: ", err);
        });
    </script>
</body>

</html>