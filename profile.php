<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $_FILES['profile_pic']['tmp_name']);
        finfo_close($finfo);

        $file_size = $_FILES['profile_pic']['size'];

        if (in_array($file_type, $allowed_types) && $file_size <= 2000000) {
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $new_filename = "user_" . $user_id . "_" . time() . "." . $ext;
            $upload_path = 'uploads/' . $new_filename;

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_path)) {
                $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
                $stmt->bind_param("si", $new_filename, $user_id);
                $stmt->execute();

                $message = "Profile picture updated successfully!";
                $messageType = "success";
            } else {
                $message = "Server Error: Failed to save the file.";
                $messageType = "error";
            }
        } else {
            $message = "Upload Failed: Must be a JPG/PNG/GIF under 2MB.";
            $messageType = "error";
        }
    }
}

$stmt = $conn->prepare("SELECT username, email, profile_picture FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

$profile_img = !empty($user_data['profile_picture']) && file_exists('uploads/' . $user_data['profile_picture'])
    ? 'uploads/' . htmlspecialchars($user_data['profile_picture'])
    : 'https://ui-avatars.com/api/?name=' . urlencode($user_data['username']) . '&background=0D8ABC&color=fff';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php"
                        class="text-gray-500 hover:text-gray-700 flex items-center transition-colors">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="p-8 border-b border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900">Profile Settings</h2>
                <p class="text-gray-500 mt-1">Manage your personal information and avatar.</p>
            </div>

            <div class="p-8">
                <?php if ($message): ?>
                    <div
                        class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'; ?>">
                        <p class="font-medium"><?php echo $message; ?></p>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col md:flex-row gap-8 items-start">

                    <div class="flex flex-col items-center space-y-4">
                        <div
                            class="relative w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg bg-gray-100 flex-shrink-0">
                            <img src="<?php echo $profile_img; ?>" alt="Profile Picture"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="text-center">
                            <h3 class="font-bold text-gray-900 text-lg">
                                <?php echo htmlspecialchars($user_data['username']); ?></h3>
                            <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($user_data['email']); ?></p>
                        </div>
                    </div>

                    <div class="flex-1 w-full mt-6 md:mt-0">
                        <form method="POST" enctype="multipart/form-data"
                            class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <h4 class="font-semibold text-gray-700 mb-4">Update Avatar</h4>

                            <label class="block text-sm font-medium text-gray-600 mb-2">
                                Choose new image (JPG, PNG, GIF up to 2MB)
                            </label>

                            <div class="flex items-center justify-center w-full mb-4">
                                <label for="dropzone-file"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                            </path>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to
                                                upload</span></p>
                                    </div>
                                    <input id="dropzone-file" type="file" name="profile_pic" class="hidden"
                                        accept="image/png, image/jpeg, image/gif" required />
                                </label>
                            </div>

                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition-colors shadow-sm">
                                Save Profile Picture
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('dropzone-file').addEventListener('change', function (e) {
            var fileName = e.target.files[0].name;
            var textElement = this.previousElementSibling.querySelector('p');
            textElement.innerHTML = '<span class="font-semibold text-blue-600">' + fileName + '</span> selected';
        });
    </script>
</body>

</html>