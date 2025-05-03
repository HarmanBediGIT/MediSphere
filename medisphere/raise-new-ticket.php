<!DOCTYPE html>
<html>

<head>
    <title>Raise New Ticket</title>
    <style>
        /* Add relevant styling for form */
        .form-container {
            width: 320px;
            margin: 40px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.7);
            border: 1px solid #dee2e6;
        }

        .form-container h2 {
            text-align: center;
            color: #343a40;
        }

        .input-field {
            margin-bottom: 20px;
        }

        .input-field input {
            width: 100%;
            padding: 12px;
            border: 1px solid #dee2e6;
            box-sizing: border-box;
        }

        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        .back-btn {
            padding: 10px;
            background-color: red;
            justify-content: right;
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .back-btn:hover {
            background-color: crimson;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Raise New Ticket</h2>
        <form action="process_new_ticket.php" method="POST">
            <label>Name :</label>
            <div class="input-field">
                <input type="text" name="name" placeholder="Enter you name" required>
            </div>
            <label>Subject :</label>
            <div class="input-field">
                <input type="text" name="subject" placeholder="Enter subject of ticket" required>
            </div>
            <label>Message :</label>
            <div class="input-field">
                <input type="text" name="message" placeholder="Enter your message" required>
            </div>
            <label>File :</label>
            <div class="input-field">
                <input type="file" name="file" placeholder="Upload file (if required)">
            </div>
            <button type="submit" class="submit-btn">Send Ticket</button>

            <button type="button" class="back-btn" onclick="window.location.href='ticketraisingpage.php'"> Back
            </button>
        </form>
    </div>
</body>

</html>