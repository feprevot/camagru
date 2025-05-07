
# 📸 Camagru

Camagru is a **mini Instagram-like web application** built as part of the 42 school curriculum.  
It allows users to **capture photos via webcam or upload PNG images**, apply fun overlays (frames, bunny ears, etc.), and share them in a public gallery. Logged-in users can **like, comment**, and receive **notifications**.

---

## ⚙️ Tech Stack

- **Backend**: PHP 8.2 (Vanilla PHP)
- **Frontend**: HTML5, CSS3, Vanilla JS
- **Database**: MySQL
- **Server**: Apache 2.4 (Dockerized)
- **Mailing**: msmtp + Gmail SMTP (for notifications)
- **Containerization**: Docker + Docker Compose
- **Other Tools**:
  - GD library for server-side image processing
  - ngrok (for development/testing image sharing)

---

## 🧱 Architecture

The project follows a classic **MVC (Model-View-Controller)** pattern:

.
├── controllers/ # Business logic & routing
├── models/ # Database access and queries
├── views/ # HTML/PHP templates for rendering
├── public/ # Public static files (CSS, JS, uploads)
├── config/ # DB connection config
└── docker/ # Apache + MySQL + SSL setup


- **Models** contain functions for interacting with users, images, likes, comments.
- **Controllers** handle routing and logic like login, registration, gallery rendering.
- **Views** include `layout.php` and pages like `edit`, `gallery`, `auth` forms.

---

## 🚀 Features

### Core Features
- ✅ User authentication (register, login, reset password)
- ✅ Email confirmation required for login
- ✅ Webcam photo capture (client-side preview)
- ✅ PNG upload with server-side processing
- ✅ Overlay filters (frames, bunny ears, etc.)
- ✅ Public gallery (infinite scroll + pagination fallback)
- ✅ Likes & comments
- ✅ Notification via email on new comment (user preference toggle)

### Bonus Features
- ✨ Infinite scroll in the gallery
- ✨ Live overlay preview on webcam
- ✨ Dark mode (creative version)
- ✨ Guest-only and auth-only layouts

---


### 📸 Image Upload Flow
- Webcam: Capture preview → send PNG → merge overlay on server (GD) → save
- Upload: User can upload PNG (JPEGs are rejected) → server saves validated file
- Images are only stored as .png and validated both client & server side.

### 📬 Email Notifications
 - On comment: Author receives an email (unless opted-out in settings)
 - SMTP configured with Gmail using msmtp (password via env variable)

###  📚 License
This project is for educational use at 42 school.
