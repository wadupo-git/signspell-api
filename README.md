# ⚙️ SignSpell API Backend (PHP)

This repository contains the backend API files for the **SignSpell** mobile application. These PHP scripts handle all data interactions, including user management, content delivery (letters and signs), activity tracking, and administrator functions.

## 🛠️ Technology Stack

| Component | Technology | Notes |
| :--- | :--- | :--- |
| **Language** | **PHP** | All API logic and database interactions are written in PHP. |
| **Database** | **MySQL** | Used to store all application data (users, words, letters, spelling history). |
| **Connection** | `db_connect.php` | Core file for establishing the database connection. |
| **Server** | **Apache** | Required to execute the PHP scripts. |

---

## 🚀 Setup and Installation

1.  **Clone the repository:**
    ```bash
    git clone [Your API Repository URL Here]
    ```
2.  **Database Configuration:**
    * Set up the **signspell_db** database in your environment.
    * Import the SQL schema file (if applicable).
    * Update the credentials and connection details in `db_connect.php`.
3.  **Deployment:**
    * Place the files (e.g., in a directory named `api/`) on your web server.
    * Ensure the `uploads/` directories (`profile_pictures/` and `video/`) have **write permissions** for file uploads.
4.  **Testing:**
    * Use the utility file `test_log_action.html` or a tool like Postman to verify API endpoint functionality.

---

## 📌 API Endpoints Reference

The files are grouped by the functionality they provide to the mobile application:

### 1. User & Authentication
| Filename | HTTP Method | Description |
| :--- | :--- | :--- |
| `login.php` | POST | Authenticates a user and returns session/profile data. |
| `register.php` | POST | Creates a new user account. |
| `get_user_details_and_history.php` | GET | Fetches a user's profile and their personal spelling history. |
| `update_user.php` | POST | Updates a user's profile information (name, email). |

### 2. Content Management (Letters & Words)
| Filename | HTTP Method | Description |
| :--- | :--- | :--- |
| `get_all_letters.php` | GET | Retrieves all letter data (including 3D animation references). |
| `get_words_by_category.php` | GET | Fetches words grouped by categories (Greetings, Family, etc.). |
| `save_spelled_word.php`| POST | Records a word spelled by the user for activity tracking. |
| `add_word.php` | POST | Admin endpoint to add a new word and its sign video. |

### 3. Administration & Stats
| Filename | HTTP Method | Description |
| :--- | :--- | :--- |
| `get_admin_stats.php` | GET | Retrieves key metrics for the Admin Dashboard (Total Users, etc.). |
| `fetch_users.php` | GET | Retrieves a full list of all registered users. |
| `delete_user.php` | POST | Admin function to remove a user account. |
| `update_letter.php` | POST | Admin function to modify existing letter content. |

---
