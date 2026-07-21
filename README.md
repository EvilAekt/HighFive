<div align="center">
  <img src="https://via.placeholder.com/150x150/000000/FFFFFF?text=HIGH+FIVE" alt="HIGH FIVE Logo" width="120" height="120">
  
  # ✋ HIGH FIVE - Modern E-Commerce
  
  **An advanced, AI-powered e-commerce platform built with Laravel.**
  
  <p>
    <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
    <img src="https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="AlpineJS">
    <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="TailwindCSS">
    <img src="https://img.shields.io/badge/Google_Gemini-4285F4?style=for-the-badge&logo=google&logoColor=white" alt="Google Gemini AI">
  </p>
</div>

---

## ✨ Overview

**HIGH FIVE** is a fully functional, highly interactive e-commerce platform designed for clothing retail. It focuses on delivering a premium user experience with modern aesthetics and cutting-edge features. 

The highlight of the platform is its **AI-Powered Customer Service**, which acts as a 24/7 intelligent sales assistant capable of answering product queries, checking stock availability, and recommending products interactively.

## 🚀 Key Features

### 🤖 Intelligent AI Chatbot (Gemini Integration)
- **Context-Aware Responses:** The bot understands the store's inventory, prices, and release dates, providing highly accurate answers.
- **Smart Recommendations:** When users ask for recommendations, the AI automatically picks the best-selling or newest products and renders them as beautiful UI **Product Cards** right inside the chat.
- **Out-of-Context Protection:** Strict prompt engineering ensures the AI stays on topic and politely ignores unrelated questions.
- **Unified Sessions:** Users' cross-device chat histories are flawlessly unified under a single account session.

### 🛍️ Storefront & E-Commerce
- **Dynamic Catalog:** Browse products with specific color and size variants.
- **Cart & Checkout:** Seamless shopping cart experience and order management.
- **Flash Sales:** Built-in support for time-limited flash sale campaigns.

### 👨‍💻 Admin & Owner Dashboards
- **Live Chat Dashboard:** Monitor and reply to customer inquiries in real-time. Admins can seamlessly take over the conversation from the AI.
- **Toggle AI/Bot Rules:** Easily switch between the advanced Gemini AI, a basic rule-based bot, or fully manual human mode.
- **Inventory Management:** Full control over product listings, variants, stock, and categories.

## 🛠️ Technology Stack

- **Backend:** Laravel 11.x (PHP 8.2+)
- **Frontend:** Blade Templates, Tailwind CSS (for styling), Alpine.js (for reactivity)
- **AI Engine:** Google Gemini Pro API (`gemma-4-26b-a4b-it`)
- **Database:** MySQL / PostgreSQL

## 📦 Installation & Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/highfive.git
   cd highfive/laravel
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure Database & AI Credentials**
   Open `.env` and set your database connection, as well as your Google Gemini API key:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=highfive_db
   DB_USERNAME=root
   DB_PASSWORD=

   GEMINI_API_KEY="your_google_gemini_api_key_here"
   ```

5. **Run Migrations & Seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Start the Application**
   ```bash
   php artisan serve
   ```

## 📸 Screenshots

*(Add screenshots of your application here! Consider adding screenshots of the AI Chatbot recommending a product, the storefront, and the Live Chat Admin Dashboard).*

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---
<div align="center">
  <i>Built with ❤️ for HIGH FIVE.</i>
</div>
