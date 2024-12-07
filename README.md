# **Helix - Trouble Ticket Management System**  

Helix is a robust web-based system designed to simplify and optimize trouble ticket management. Built with efficiency and user experience in mind, Helix empowers clients, agents, and administrators to handle support tickets effortlessly.  

---

## **Features**  
### **For Clients**  
- Submit and track trouble tickets in real-time.  
- Update tickets with additional information.  
- Communicate directly with agents for faster resolution.  

### **For Agents**  
- View and manage tickets by department.  
- Assign tickets, update statuses, and collaborate effectively.  
- Utilize FAQs for quick ticket resolution.  

### **For Administrators**  
- Manage users, departments, and ticket statuses.  
- Monitor system performance with key metrics.  
- Oversee all tickets and ensure smooth operation.  

---

## **Technologies Used**  
- **Frontend**:  
  - HTML5 for page structure.  
  - CSS3 for styling and responsive design.  
  - JavaScript (with Ajax) for interactive user experiences.  
- **Backend**:  
  - PHP for server-side logic.  
  - MySQL for database management.  
- **Database Access**:  
  - Secure interactions with PDO (PHP Data Objects).  
- **Security Features**:  
  - Protection against SQL Injection, XSS, and CSRF attacks.  
  - Passwords hashed using `password_hash()` for enhanced security.  

---

## **How to Run**  
1. Clone this repository:
   
   ```bash  
   git clone https://github.com/LuisGouveiaCloudFy/Helix-Ticket-System
   ```
   
3. Set up a local server (e.g., XAMPP or WAMP).  
4. Import the `ticket_system.sql` file into your MySQL database using phpMyAdmin.  
5. Configure the database connection in the `/config/db.php` file.  
6. Open the application in your browser and start managing tickets!  

---

## **Contributions**  
We welcome contributions! If you'd like to improve Helix:  
- Fork this repository.  
- Submit your changes via a pull request.  
- Report issues or suggest enhancements in the Issues tab.  

---

**Helix**: Simplifying ticket management, one issue at a time.  
