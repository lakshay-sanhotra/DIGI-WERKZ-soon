<?php
// Start session to manage session data
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $emailId = trim($_POST['emailId']);
    $mobileNumber = trim($_POST['mobileNumber']);

    // Remove non-numeric characters (if any) and cast to integer
    $mobileNumber = (int)preg_replace('/\D/', '', $mobileNumber);  // Remove all non-numeric characters and convert to integer

    $apiKey = "rc2A8ranm8PBehbN50V3u26AMX7ikVgY4BaH0oCViGU";

    // Validate inputs
    if (empty($name) || empty($emailId) || empty($mobileNumber)) {
        $error = "All fields are required!";
    } else {
        // Prepare user details to send to the API
        $userDetails = [
            'name' => $name,
            'emailId' => $emailId,
            'mobileNumber' => $mobileNumber  // Send the mobile number as an integer
        ];

        // Initialize cURL request to get session ID
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://41.216.166.147:8081/v1/user/details");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "x-api-key: $apiKey"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userDetails));

        // Execute cURL request
        $response = curl_exec($ch);

        // Error handling for cURL
        if (curl_errno($ch)) {
            $error = "Error fetching session ID: " . curl_error($ch);
        } else {
            $responseData = json_decode($response, true);

            // Debugging: output the response to understand the issue
            // var_dump($responseData);  // For debugging purposes

            if (isset($responseData['success']) && $responseData['success'] === true && isset($responseData['data']['sessionId'])) {
                // Store session ID in session
                $_SESSION['sessionId'] = $responseData['data']['sessionId'];
                header("Location: " . $_SERVER['REQUEST_URI']);
            } else {
                $error = "Failed to get session ID. API response: " . json_encode($responseData);
            }
        }

        // Close cURL connection
        curl_close($ch);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Addmie Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        /* Import Google font - Poppins */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: #E3F2FD;
        }

        .main-container {
            height: 100vh;
            display: flex; /* Enable flexbox for vertical centering */
            align-items: center; /* Vertically center content */
            justify-content: center; /* Horizontally center content */
        }

        .container-content { /* New container for content within the main container */
            width: 90%; /* Occupy most of the width on larger screens */
            max-width: 900px; /* Set a maximum width */
            border-radius: 1rem;
            overflow: hidden; /* Ensure rounded corners clip properly */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
        }


        .left-section {
            background-color: #fff6d6;
            text-align: center;
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center; /* Vertically center content */
        }

        .left-section h5 {
            font-weight: bold;
            margin-bottom: 1rem; /* Add some spacing */
        }

        .left-section h2 {
            color: red;
            font-weight: bold;
            margin-bottom: 2rem; /* Add some spacing */
        }

        .left-section img {
            width: 100%;
            max-width: 100%; /* Make image responsive */
            height: auto; /* Maintain aspect ratio */
        }

        .form-section {
            /* padding: 30px; Reduced padding */
            background-color: white;
        }

        .form-control {
            height: 50px;
            font-size: 18px;
        }

        .btn-custom {
            width: 100%;
            background: red;
            color: white;
            font-size: 20px;
            font-weight: bold;
            padding: 12px;
            border: none; /* Remove default button border */
            border-radius: 5px; /* Add rounded corners to the button */
            transition: background-color 0.3s ease; /* Add a smooth transition */
        }

        .btn-custom:hover {
            background: darkred;
        }

        .terms {
            font-size: 14px;
            margin-top: 1rem; /* Add some spacing */
        }

        /* Chatbot Styles */
        .chatbot {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 20px; /* Space the chatbot from the form */
        }

        .chatbot header {
            background-color: #fc151d;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .chatbox {
    height: 75vh; /* Desktop view */
    overflow-y: auto;
    padding: 1rem;
}

@media (max-width: 768px) {
    .chatbox {
        height: 50vh; /* Mobile view */
    }
}


        .chat {
            margin-bottom: 1rem;
            display: flex;
        }
        .incoming{
            flex-direction: row;
        }

        .outgoing{
            flex-direction: row-reverse;
        }

        .incoming span { /* Style for bot avatar */
            background-color: #fc151d;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
        }

        .chat p {
            background-color: #FFF6D6; /* Light gray for user messages */
            color: #333;
            padding: 10px;
            border-radius: 8px;
            max-width: 70%; /* Prevent messages from overflowing */
        }
        .incoming p{
            background-color: #fc151d;
            color: white;
        }

        .chat-input {
            display: flex;
            padding: 10px;
            background-color: #fff;
            border-top: 1px solid #ddd;
        }

        .chat-input textarea {
            flex-grow: 1;
            border: none;
            outline: none;
            resize: none;
            padding: 8px;
            border-radius: 5px;
            font-size: 1rem;
        }

        .chat-input span {
            color: #fc151d;
            cursor: pointer;
            margin-left: 0.5rem;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }


        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .left-section {
                padding: 20px;
            }

            .left-section img {
                width: 150px;
            }

            .form-section {
                padding: 20px;
            }

            .btn-custom {
                font-size: 18px;
                padding: 10px;
            }
            .container-content {
                width: 95%;
            }
        }

        .btn:hover {
    color: white;
}
    </style>
</head>
<body>

<div class="container-fluid main-container">
    <div class="container-content row shadow-lg rounded-4 overflow-hidden">
        <div class="col-md-4 col-12 left-section">
            <h5>Ask me anything about</h5>
            <h2>Addmie</h2>
            <img src="website-logo.png" alt="Character Image" class="img-fluid d-none d-md-block">
        </div>

        <div class="col-md-8 col-12 form-section">
        <?php if (isset($_SESSION['sessionId'])){ ?>

            <div class="chatbot" >
                
                <ul class="chatbox" id="chat">
                    <li class="chat incoming">
                        <span class="material-symbols-outlined">smart_toy</span>
                        <p>Hi there 👋<br>How can I help you today?</p>
                    </li>
                </ul>
                <div class="chat-input">
                    <textarea id="chatInput" placeholder="Enter a message..." spellcheck="false" required></textarea>
                    <span id="sendChatBtn" class="material-symbols-rounded">send</span>
                </div>
            </div>

<? }else{ ?>


            <form class="pt-5 mt-5" action="" method="POST">
                <input type="text" name="name" class="form-control mb-3" placeholder="Enter your name" required>
                <input type="text" name="mobileNumber" class="form-control mb-3" placeholder="Enter your mobile number" required>
                <input type="email" name="emailId" class="form-control mb-3" placeholder="Enter your email" required>
                <button type="submit" class="btn btn-custom">Start Your Conversation</button>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" required>
                    <label class="form-check-label terms">
                        I agree to all <a href="#" class="text-decoration-none">Terms and Conditions</a> and <a href="#" class="text-decoration-none">Privacy Policies</a>.
                    </label>
                </div>
            </form>

            <? } ?>





        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const chatInput = document.getElementById("chatInput");
    const sendChatBtn = document.getElementById("sendChatBtn");
    const chatBox = document.getElementById("chat");

    const createChatLi = (message, className) => {
        const chatLi = document.createElement("li");
        chatLi.className = `chat ${className}`;  // Adding both "chat" and dynamic class (outgoing/incoming)

        if (className === "outgoing") {
            chatLi.innerHTML = `<p>${message}</p>`;
        } else {
            chatLi.innerHTML = `<span class="material-symbols-outlined">smart_toy</span><p>${message}</p>`;
        }

        return chatLi;
    };

    const handleChat = async () => {
        const userMessage = chatInput.value.trim();
        if (!userMessage) return;

        // Append the outgoing message with the "chat outgoing" class
        chatBox.appendChild(createChatLi(userMessage, "outgoing"));
        chatInput.value = "";

        // Display the thinking message with the "chat incoming" class
        const thinkingMessage = createChatLi("Thinking...", "incoming");
        chatBox.appendChild(thinkingMessage);

        const formData = new FormData();
        formData.append("message", userMessage);

        try {
            const response = await fetch("chatbot.php", { method: "POST", body: formData });
            const data = await response.json();

            // Update thinking message with response or error
            thinkingMessage.querySelector("p").textContent = data.success ? data.response : `Error: ${data.error}`;
        } catch (error) {
            thinkingMessage.querySelector("p").textContent = `Error: ${error.message}`;
        }
    };

    sendChatBtn.addEventListener("click", handleChat);

    chatInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            handleChat();
        }
    });
</script>

</body>
</html>