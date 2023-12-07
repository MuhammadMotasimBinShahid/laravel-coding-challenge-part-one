<div align='center'>

<a href="https://laravel.com" target="_blank">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<h1>Laravel Coding Challenge Part One 💻</h1>
<p>Welcome to the Laravel Coding Challenge Part One, an opportunity to showcase your expertise in Laravel 9, PHP 8, and Bootstrap 5. This challenge is meticulously crafted to assess your ability to implement common connection features akin to those found in leading social media platforms.</p>

</div>

## 📔 Table of Contents

1. [🌟 About](#star2-about)
2. [🧰 Getting Started](#toolbox-getting-started)
    - [‼️ Prerequisites](#bangbang-prerequisites)
    - [⚙️ Installation](#gear-installation)
    - [🏃 Run Locally](#runner-run-locally)
3. [🏆 Challenge](#trophy-challenge)
    - [🌍 Real World Use Case](#globe_with_meridians-real-world-use-case)
    - [✅ Objective](#dart-objective)
        - [📜 User Stories](#story-user-stories)
        - [👀 Additional Requirements](#bulb-additional-requirements)
4. [💻 Coding and Naming Conventions](#computer-coding-and-naming-conventions)
5. [👋 Contributing](#wave-contributing)
6. [⚖️ License](#scroll-license)


## :star2: About

This repository hosts the solution for the Laravel Coding Challenge Part One. The challenge aims to evaluate proficiency in Laravel 9, PHP 8 and Bootstrap 5 by implementing common connection features found in social media platforms. 

### :lightbulb: Tech Stack

- Laravel 9, PHP 8
- Bootsrap 5
- MySQL

## :toolbox: Getting Started

### :bangbang: Prerequisites

- [Composer](https://getcomposer.org/)
- [MySQL](https://www.mysql.com/)

### :gear: Installation

This project uses [Composer](https://getcomposer.org/) to manage dependencies.
 ```bash
   composer install
   ```

### :runner: Run Locally

1. Clone the project

```bash
   git clone https://github.com/MuhammadMotasimBinShahid/laravel-coding-challenge-part-one.git
```

2. Go to the project directory

```bash
   cd laravel-coding-challenge-part-one
```

3. Create a database, name it 'laravel_coding_challenge_part_one'.


4. Setup .env file

```bash
   cp .env.example .env
```

5. Install dependencies

```bash
   composer install
```

6. Generate key

```bash
   php artisan key:generate
```

7. Run migrations and seeders

```bash
   php artisan migrate --seed
```

8. Run the server

```bash
   php artisan serve
```


## :trophy: Challenge

### :globe_with_meridians: Real World Use Case

Immerse yourself in a scenario that goes beyond the lines of code. This challenge is carefully crafted to reflect the intricacies and dynamism of social media connections in the real world.

### :dart: Objective

Your task is to develop common connection features found in social media platforms.

#### :story: User Stories

- **Suggestions:** Clicking on "Suggestions" should display users not yet connected, whom you haven't invited, and who haven't sent you an invitation.

- **Connect:** Clicking on "Connect" should send a connection request to the selected user.

- **Sent Requests:** View all connection requests you've sent.

- **Withdraw Request:** Withdraw a connection request you've sent.

- **Received Requests:** View all connection requests sent to you.

- **Accept:** Accept a connection request and add the user to your network.

- **Connections:** View all your connections.

- **Remove Connection:** Remove a user from your network.

- **Connections in Common:** View all connections in common with a selected user.

#### :bulb: Additional Requirements

- Only 10 entries should be shown at the beginning. Clicking "Load more" should load 10 more entries at a time. Once there are no more entries, the "Load more" button should disappear. This applies to "Connections", "Sent Requests", "Received Requests", "Connections" as well as "Connections in common".
- The brackets should always contain the total number of the respective categories.
- To test all of the above, include seeders and factories to generate suggestions, requests by other users and connections in common with the command:
       
    DatabaseSeeder.php
    ```php artisan migrate --seed```. Connections could then be tested by accepting a request.

    ```
    <?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Database\Seeders\UsersSeeder;
    use Database\Seeders\RequestsSeeder;
    use Database\Seeders\ConnectionsInCommonSeeder;
    use Illuminate\Database\Console\Seeds\WithoutModelEvents;

    class DatabaseSeeder extends Seeder
    {
        /**
         * Seed the application's database.
         *
         * @return void 
         */
        public function run()
        {
            $this->call(UsersSeeder::class);
            $this->call(RequestsSeeder::class);
            $this->call(ConnectionsInCommonSeeder::class);
        }
    }    
    
    ```
  
- If there are no connections in common, the button should get the class 'disabled'.
- All new routes should be written into a new routing file (e.g. userConnection.php), that must be registered inside the RouteServiceProvider.php file.
- There are blade components such as connection, request and so on. You can use all of them or decide to only use one component with if clauses.
- Before each ajax call (only for getting suggestions, request and connections), show the loading skeletons and hide them after a successful response from the server.
- Javascript code should be written in their respective files and included in the main.js file.

## :computer: Coding and Naming Conventions

- Follow the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard.
- Name HTML ids in snake_case.
- Javascript & PHP variables in camelCase.
- Make sure that your code is readable for others and include comments in important places. (Guideline: Code = How?, Comment = What?)

## :wave: Contributing

Contributions are always welcome!

If you'd like to contribute to this project, must go through a pull request and be approved by a core developer before being merged. This is to ensure a proper review of all the code.

I would love pull requests! If you wish to help.

Please read the [Contributing Guidelines](CONTRIBUTTING.md).

## :scroll: License

This project is licensed under the terms of the [MIT license](LICENSE).
