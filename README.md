# Language Learning 
### Project Summary
Our application aspires to provide an engaging online environment that allows users to comprehensively learn new languages with fellow passionate learners while being assisted by various learning resources. 
Our focus extends beyond mere language acquisition - we strive to facilitate a comprehensive language learning journey. 
Given this high-level goal, we consider the domain of our application to be language learning. 

### Description
- Designed and implemented with team, a robust database schema of 28 tables BCNF normalized with 230 instances supporting various aspects of language learning for a user, tracked progress using GitHub.
- Utilized Oracle with SQLPlus for DBMS and PHP for backend/SQL connection
- Developed SQL queries to retrieve and update information, forum interactions, and learning resources for 14 language dialect combinations for a user

---

# Milestone 3 Task Timeline 

## Welcome/Home Page - Sohbat
### Task 1: Interface for users to create an account with valid information (according to DB design)
##### Timeline: March 15 - 22
- Determine the layout and design of the welcome/create account page.
- Decide on the placement of text inputs and buttons on the GUI.
##### GUI COMPONENTS:
- Implement the text input fields for User attributes like username, password and age.
- Create the "Start Learning Journey" button and integrate its functionality of adding the user to the DB and assign a unique User Id or allow the user to choose the username.
- Ensure proper validation for input fields (eg username uniqueness). 
    - Could add funcitonality of the text box highlighting itself indicating the validation.

### Task 2: Interface for users to act as a home page to connect to other webpages and choose languages
##### Timeline: March 23 - April 5
- Plan the layout and design of the Home Page.
- Determine the placement and appearance of the "Select Language" button and "Submit" button, “View Current Languages” button, “Practice Language” button, and “Language Interaction” button.
##### GUI COMPONENTS
- Implement a "Select Language" language selection page which intiates the process of selecting a language with the following funtionality:
    - Implement two dropdown menus for selecting language and dialect combinations.
    - Ensure the dropdown menu displays all available language and dialect options dynamically.
    - Click the "Submit" button to add the selected language to the current languages.
    - Ensure there is no double selecting a language and dialect combination.
- Implement a “View languages” button that allows the user to view and edit the languages the user is currently learning.
    - Display the selected languages as a table.
    - Add a "Delete" button which DELETES one or more (based on the user's choice) of the selected languages displayed in the table.
- Implement a “Practice language” button that allows the user to navigate to the page where we can get the resources for any language practice.
- Implement a “Language Interaction” button that allows the user to navigate to the page where we can get the information on the language expert, assigned to the user (according to the user's preference) and edit forum interaction.

## Language Practice Page - Annie  
### **Task 1:** Interface for the user to input Language and Dialect to search for resources.  
##### **Timeline:** Mar 15 - Mar 22  
##### **GUI Components:**  
- Implement 4 "view" buttons to allow users to view all materials, books, apps, and websites using select statements.  
- Implement 3 "add" buttons to allow users to INSERT materials that are books, apps, or websites.  
- Implement an "update material" button to allow users to UPDATE the material name and purpose after adding a material.  
- Implement a "delete material" button to allow users to DELETE materials.  

### **Task 2:** Interface for the user to choose and complete exercise questions.  
##### **Timeline:** Mar 23 - Apr 5  
##### **GUI Components:**  
- Implement user input to choose language and dialect.  
- View a list of relevant exercises based on the given language and dialect using a select statement. Connect each exercise to a question page.  
    - On the quesiton page, display relevant questions using a select statement.  
    - Implement a "mark complete" button to mark a question as complete.  
- Allow user to view the max, min, and/or average score for exercises grouping by language and using aggregation.  
- Allow user to view the count of exercise scores for each language that are above average using nested aggregation and group by statement.  

## Language Interaction Page - Romina  
### **Task 1:** Interface for the user to view, select, and remove their assigned expert.
##### **Timeline:** Mar 15 - Mar 22 
- Will include an insert operation here as a part of enabling the user to select an expert.
- Will include an update operation here as a part of enabling the user to change their assigned expert.
- Will include a delete operation here as a part of enabling the user to remove their assigned expert.
- Will include a select operation here as a part of filtering the experts by their specializations.
- Will include a projection operation here as a part of displaying the available experts to the user.
##### **GUI Components:** 
- View all available experts by clicking the “View Experts” button and provide the option to filter experts by their specialization.
- View your current expert by clicking the “View My Expert” button.
- Remove your currently assigned expert by inputting their email into a textbox and clicking the “Remove My Expert” button.
- Get assigned to an expert by selecting an available expert from the dropdown options and then clicking the “Select this Expert” button.
- Note that a user can be assigned to at most one expert at a time, so they must first remove their current expert being attempting to select a new expert. 

### **Task 2:** Interface for the user to view, join, and leave forums.
##### **Timeline:** Mar 23 - Apr 5
- Will include an insert operation here as a part of enabling the user to join a forum.
- Will include a delete operation here as a part of enabling the user to leave a forum.
- Will include a projection operation here as a part of displaying the available forums to the user.
##### **GUI Components:** 
- View all available forums by clicking the “View Forums” button.
- View all the forums you’ve joined by clicking the “View My Forums” button.
- Join a new forum by selecting a forum from the dropdown options.
- Leave a forum you’re currently a part of by inputting its URL into a textbox and clicking the “Leave Forum” button.

## Other Tasks - Sohbat, Annie, Romina  
### **Task 1:** Create a single SQL script to create all the tables and data in the database
##### **Timeline:** Mar 15 - Mar 22 
### **Task 2:** Connect each section to the DB
##### **Timeline:** Mar 15 - Mar 29 
### **Task 3:** Ensure that all sections are well integrated/connected to each other
##### **Timeline:** Mar 15 - Apr 5
### **Task 4:** Ensure that all SQL operations required for Milestone 4 are included in the project
##### **Timeline:** Mar 15 - Apr 5


# Milestone 3 Challenges
After consulting with our TA and learning about potential asynchrony issues that may be encountered with Java/Oracle-based projects, we decided to switch to our tech stack to use PHP/Oracle instead. Given that we are all new to using PHP, we anticipate that this may cause a slight challenge as we start implementing our project. 

In addition to the above, given that we haven’t covered assertions in our lectures, we also won’t be able to capture some of the participation constraints that appeared in our ER model within our SQL implementation. This will cause a slight challenge since we are not able to represent all the integrity constraints inherent to our application within our implementation.

---

# Milestone 4 Extra Info 
We have no extra information to include that has not already been included in our previous deliverables.
