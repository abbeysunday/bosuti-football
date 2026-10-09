# -*- coding: utf-8 -*-
"""Sports Event Management System report: Chapters Three to Five, references and appendices."""
import os

import content2  # reuses the mobile screenshot strip

SHOTS = os.path.dirname(os.path.abspath(__file__))
D = lambda *p: os.path.join(SHOTS, *p)


def build(g):
    chapter_three(g)
    chapter_four(g)
    chapter_five(g)
    references(g)
    appendices(g)


# =========================================================================== CHAPTER THREE
def chapter_three(g):
    P, bullets, numbered, h2, h3, chapter, table, figure = g["P"], g["bullets"], g["numbered"], g["h2"], g["h3"], g["chapter"], g["table"], g["figure"]
    inch = g["inch"]

    chapter("THREE", "METHODOLOGY AND SYSTEM ANALYSIS")
    h2("3.1 Research Design")
    P("The study adopts a system development research design: the problem domain is analysed, requirements are identified, and a web-based information system is designed, implemented and evaluated against those requirements. This chapter presents the development approach, the methods used to gather requirements, the analysis of the existing process, the requirements of the new system and its design — architecture, use cases, data flow, database, algorithms, interfaces, security and testing strategy.")

    h2("3.2 Development Approach")
    P("An <b>iterative and incremental</b> development approach was adopted (Pressman and Maxim, 2020; Sommerville, 2016). The project has identifiable modules with dependencies: teams must exist before fixtures are created, fixtures must exist before scores are attached, and results and standings are produced from valid scores. Each increment was designed, implemented, tested and reviewed before the next began, and feedback from testing was used to correct defects and improve usability (Table 3.1).")
    table("3.1: Development increments", ["Increment", "Deliverables"], [
        ["1. User interface", "Responsive public website design converted into reusable Blade layouts and components."],
        ["2. Data model", "Relational schema (migrations), Eloquent models and relationships for seasons, competitions, teams and players."],
        ["3. Fixtures and results", "Fixture scheduling, result entry, match events, line-ups; league table and statistics services."],
        ["4. Administration", "Role-protected admin dashboard with create, view, update and delete screens for every entity."],
        ["5. Public information", "Public pages connected to the database: fixtures, results, standings, teams, squads, player profiles, news and media."],
        ["6. Registration and communication", "User accounts, trial applications, contact messages and administrative inboxes."],
        ["7. Supporting features", "Rich-text news editor with sanitisation, image optimisation, CSV import, sample-season generator, basketball and facilities pages, fixture conflict checking."],
        ["Continuous", "Automated tests written alongside each increment and re-run after every change."],
    ], [1.7 * inch, 4.3 * inch])

    h2("3.3 Methods of Data Collection")
    P("Requirements were gathered through the following methods:")
    bullets([
        "<b>Observation</b> of how sports information is currently handled — fixtures and results announced on notice boards and in messaging groups, tables computed by hand.",
        "<b>Informal discussions</b> with students involved in the internal teams about the information they need and the difficulties they face.",
        "<b>Review</b> of existing sports-competition websites and of the literature in Chapter Two to identify the standard functions of fixture, result and standings management.",
        "<b>A structured questionnaire</b> (Appendix C) was designed to measure user satisfaction after deployment. It had not been administered at the time of writing; its results should be added once it has been completed by representative users.",
    ])

    h2("3.4 Analysis of the Existing System")
    P("In the existing process, registration of players occurs through paper lists or spreadsheets kept by team officials; fixtures are agreed among organisers and announced on notice boards or in messaging groups; scores are reported verbally or by message after each match; any league table is computed by hand; and records of scorers and cards, if kept at all, are held privately by individual organisers.")
    h3("3.4.1 Weaknesses of the existing system")
    bullets([
        "<b>Fragmentation:</b> each activity produces information that another needs, but there is no shared store, so data are re-entered and become inconsistent.",
        "<b>Scheduling conflicts:</b> nothing prevents a team from being given two matches on the same day, and changes do not reach everyone.",
        "<b>Error-prone standings:</b> hand-computed tables are slow to update and prone to arithmetic and ordering errors.",
        "<b>Poor accessibility:</b> information in chat groups is buried quickly and unavailable to students outside the groups.",
        "<b>Loss of history:</b> results and statistics of previous seasons are not preserved.",
        "<b>No formal channels</b> for registration, trial applications or enquiries, and no control over who sees participants' personal data.",
    ])

    h2("3.5 The Proposed System")
    P("The proposed system is a web-based platform with two interfaces sharing one database: a <b>public website</b> for participants and spectators, and an <b>administration dashboard</b> for authorised organisers. Organisers record each fact once — a team, a player, a fixture, a score, a goal — and the system derives everything else: schedules, standings, player and team statistics, the match centre and the homepage summaries. The database becomes the controlled source of event information, while the interfaces provide role-appropriate access.")
    table("3.2: Technologies selected and justification", ["Layer", "Technology", "Justification"], [
        ["Presentation", "HTML5, CSS3, JavaScript; Blade templates; Tailwind CSS 3 and Alpine.js 3 (admin)", "Standard web technologies; responsive layouts; lightweight for mobile data connections."],
        ["Application", "PHP 8.2 with the Laravel 12 framework", "MVC structure, routing, validation, authentication, CSRF protection and testing tools out of the box; widely hosted."],
        ["Database", "MySQL / MariaDB 10.4 (SQLite in-memory for tests)", "Reliable open-source RDBMS with foreign keys and transactions; bundled with XAMPP."],
        ["Authentication", "Laravel Breeze", "Secure registration, login, password reset and password hashing."],
        ["Development", "Visual Studio Code, XAMPP, Composer, Node.js / Vite", "Free, widely used local development and build tools."],
        ["Testing", "Pest 3 (on PHPUnit); headless Microsoft Edge", "Automated feature tests; automated responsive checks."],
    ], [1.1 * inch, 2.2 * inch, 2.7 * inch])

    h2("3.6 Functional Requirements")
    table("3.3: Functional requirements", ["ID", "Requirement", "Description"], [
        ["FR01", "User registration", "The system shall allow users to create accounts with a name, a valid unique email and a confirmed password, and students to apply for team trials."],
        ["FR02", "Authentication", "The system shall authenticate users before protected operations and allow them to log out."],
        ["FR03", "Team management", "The administrator shall create, view, update and archive teams (name, short name, colours, logo, coach, captain)."],
        ["FR04", "Player management", "The system shall record athletes and associate each with a team; shirt numbers shall be unique within a team."],
        ["FR05", "Event management", "The administrator shall create and manage seasons and competitions (league, cup, friendly, tournament)."],
        ["FR06", "Fixture management", "The administrator shall create and update fixtures; a team shall not play itself or be scheduled for two active fixtures on the same day."],
        ["FR07", "Score recording", "Authorised users shall record scores against valid fixtures, together with match events (goals, assists, cards, substitutions) and line-ups restricted to the fixture's teams."],
        ["FR08", "Result publication", "A result shall become public when the fixture is marked full time; the league table shall be computed automatically from completed results."],
        ["FR09", "Information access", "Participants and spectators shall view schedules, results, standings, teams, squads, player profiles, news and media without logging in."],
        ["FR10", "Reporting", "The system shall provide organised views: fixtures filtered by competition, team and month; a match centre; standings with form guide; player and team statistics; an administrative dashboard."],
        ["FR11", "Communication", "The system shall accept contact messages and trial applications and provide administrative inboxes with status tracking."],
        ["FR12", "Content", "The administrator shall publish news (rich text), photos and videos."],
        ["FR13", "Data import", "The system shall import teams, players, staff, fixtures and events from CSV files, rejecting the whole import if any row is invalid."],
    ], [0.55 * inch, 1.25 * inch, 4.2 * inch])

    h2("3.7 Non-Functional Requirements")
    table("3.4: Non-functional requirements", ["Category", "Requirement"], [
        ["Usability", "Interfaces shall be clear, consistent and easy to navigate, with labelled forms and specific error messages beside each field."],
        ["Performance", "Common pages shall return within one second under the intended small-organisation workload; statistics shall use aggregate queries; images shall be optimised."],
        ["Security", "Protected functions shall require authentication and the administrator role; all input shall be validated on the server; output shall be escaped."],
        ["Reliability", "Valid records shall be preserved; deleting teams, players or news shall not destroy historical match records."],
        ["Maintainability", "Code shall follow MVC with services for business logic, form requests for validation and reusable view components."],
        ["Compatibility", "The application shall work in modern browsers (Chrome, Edge, Firefox, Safari)."],
        ["Responsiveness", "Pages shall be usable on screens from 320 px to 1440 px wide without horizontal scrolling."],
        ["Scalability", "Additional seasons, competitions, teams and records shall be added through data, without redesigning the application."],
        ["Privacy", "Matriculation numbers, state of origin and applicants' contact details shall never appear on public pages."],
        ["Availability", "Users shall be able to access the platform whenever the hosting environment and network are available."],
    ], [1.3 * inch, 4.7 * inch])

    h2("3.8 Feasibility Analysis")
    h3("3.8.1 Technical feasibility")
    P("The technology stack is mature and widely documented. HTML, CSS and JavaScript provide the browser interface, PHP with Laravel provides server-side processing, and MySQL stores relational data. Visual Studio Code and XAMPP provide a complete local development environment, and the finished system runs on any host supporting PHP 8.2 and MySQL, including low-cost shared hosting.")
    h3("3.8.2 Operational feasibility")
    P("The system's functions correspond closely to tasks organisers already perform, so the main operational change is how information is recorded and retrieved. Training focuses on the administrator workflow (Appendix F); spectators need no training because the public site requires no login.")
    h3("3.8.3 Economic feasibility")
    P("All development tools and frameworks used are free and open source. The running cost is limited to hosting and a domain name, which is low for an educational institution.")
    h3("3.8.4 Schedule feasibility")
    P("Academic time constraints were managed by prioritising the essential modules — authentication, registration, teams, competitions, fixtures, scores and results — and adding supporting features in later increments.")

    h2("3.9 System Architecture")
    P("The system follows a client-server, three-tier architecture implemented with Laravel's MVC structure (Figure 3.1). The browser forms the presentation tier; the Laravel application forms the application tier; and MariaDB/MySQL forms the data tier. The request flow is:")
    numbered([
        "The user opens a page or submits a form in the browser.",
        "The request reaches a route; middleware applies authentication, the administrator check, CSRF verification and rate limiting.",
        "A form request validates the input (for example, the fixture conflict rule).",
        "A controller coordinates the request, delegating business logic such as standings to a service class.",
        "Eloquent models read from or write to the database using parameterised queries.",
        "The database returns the requested records or confirms the operation.",
        "A Blade view renders the schedule, score, result or confirmation, which is returned to the browser.",
    ])
    figure(g["architecture_diagram"](), "3.1: System architecture")

    h2("3.10 Use Case Model")
    P("Three actors interact with the system (Figure 3.2). A <b>Visitor</b> (spectator) browses public information. A <b>Student</b> (participant) is a visitor who registers an account, applies for trials or sends a message. An <b>Administrator</b> is an authenticated organiser with the admin role who manages all data. Table 3.5 describes the principal use case.")
    figure(g["use_case_diagram"](), "3.2: Use case diagram")
    table("3.5: Use case description — Record a match result", ["Item", "Description"], [
        ["Actor", "Administrator"],
        ["Pre-condition", "The administrator is logged in; the fixture exists."],
        ["Main flow", "1. Open Fixtures and select the match. 2. Choose status “Full time” and enter both scores. 3. Optionally add referee, attendance and a summary. 4. Save. 5. Add goal, assist, card and substitution events; for each, choose the team — the player list shows only that team's players. 6. Tick each team's line-up."],
        ["Alternative flow", "A score is missing for a completed match: a validation message is shown and nothing is saved. A player not in the selected team: the event is rejected."],
        ["Post-condition", "The public result, league table, team and player statistics, homepage and match centre reflect the result immediately."],
    ], [1.4 * inch, 4.6 * inch])

    h2("3.11 Data Flow Design")
    P("Figure 3.3 shows the context-level (level-0) data flow diagram. Visitors and students send requests, registrations, trial applications and contact messages, and receive published pages; administrators supply competition data and content and receive the dashboard, statistics and inboxes; the system can notify the organisers by email when a contact message arrives. Because all flows pass through one database, no separate copies of the same information are maintained.")
    figure(g["context_dfd"](), "3.3: Context diagram (level-0 data flow diagram)")

    h2("3.12 Database Design")
    P("The database was designed in third normal form. Each real-world entity has its own table with a surrogate primary key, and relationships are enforced by foreign keys. The conceptual tables identified during analysis map onto the implemented schema as shown in Table 3.6. Two design decisions are significant. First, the general “event” is modelled as a <b>season</b> containing one or more <b>competitions</b>, so the same structure supports leagues, cups, friendlies and tournaments. Second, results and standings are <b>not stored separately</b>: a fixture's status and scores are its result, and the standings are computed from completed fixtures, which removes update anomalies. Figure 3.4 shows the entity-relationship diagram; the full data dictionary is in Appendix A.")
    table("3.6: Conceptual tables and the implemented schema", ["Conceptual table", "Implemented as", "Notes"], [
        ["users", "users", "name, email, hashed password, role (admin | user)."],
        ["teams", "teams", "Soft-deleted so past results keep their names."],
        ["players", "players", "team_id foreign key; position, shirt number; private fields hidden from the public."],
        ["events", "seasons, competitions", "A season (e.g. 2026/2027) contains competitions of type league, cup, friendly or tournament."],
        ["matches", "fixtures", "home and away team, date, kick-off, venue, status (scheduled, live, completed, postponed, cancelled), scores."],
        ["results", "fixtures.status + scores; computed standings", "A result is public once status is “completed”; standings are derived, never typed."],
        ["(new)", "match_events, fixture_players", "Goals, assists, cards and substitutions; line-ups. Source of player statistics."],
        ["(new)", "news_posts, gallery_items, videos, staff, trial_applications, contact_messages", "Content, people and communication."],
    ], [1.25 * inch, 1.75 * inch, 3.0 * inch])
    P("Deletion rules were chosen deliberately: seasons, competitions and teams referenced by fixtures are protected (<i>restrict</i>); match events and line-ups are removed with their fixture (<i>cascade</i>); optional links such as a photo's fixture are cleared (<i>set null</i>); and teams, players, news and staff are soft-deleted so that historical results remain correct.")
    figure(g["erd_diagram"](), "3.4: Entity-relationship diagram")

    h2("3.13 Algorithm Design")
    h3("3.13.1 Standings computation")
    P("The standings are computed on demand by the <i>LeagueTableService</i> (Figure 3.5). Every team drawn in the competition receives a row. Completed fixtures are processed in date order: both teams' played, goals for and goals against are updated; the result adds a win, draw or loss and 3, 1 or 0 points; and the result is appended to the team's form. Rows are sorted by points, goal difference, goals for and name. Because the table is a function of the results, entering or correcting any result is reflected immediately and consistently.")
    figure(g["league_flowchart"](), "3.5: Flowchart of the standings algorithm")
    h3("3.13.2 Fixture conflict detection")
    P("When a fixture is created or edited, after the normal field validation passes, the system searches for another fixture on the same date, involving either of the two teams, whose status is not postponed or cancelled (the fixture being edited is excluded). If one exists, the save is rejected with a message naming the clashing match. A team therefore cannot be scheduled for two active matches on the same day, and postponing a match frees the day for rescheduling.")
    h3("3.13.3 Player statistics")
    P("The <i>PlayerStatisticsService</i> derives each player's appearances (line-up or event involvement), goals, assists, yellow and red cards and, for goalkeepers, clean sheets, from live and completed matches only, using aggregate SQL queries rather than loading records into memory.")

    h2("3.14 Input and Output Design")
    P("<b>Inputs</b> are captured through validated forms: registration and login forms; administrative forms for every entity, with dropdowns restricted to valid options, date and time pickers and image inputs limited to JPG, PNG and WebP; the result, event and line-up forms on the match page; the public trial and contact forms, protected by a hidden honeypot field and rate limiting; and CSV files for bulk import. Validation errors are displayed beside the relevant field with an icon and message, not colour alone.")
    P("<b>Outputs</b> are the public pages — homepage, fixtures and results, match centre, standings with form guide and top scorers, teams, squads, player profiles, news, gallery, videos, staff, basketball and facilities pages — and the administrative dashboard with counts, upcoming fixtures, recent results and inbox alerts.")

    h2("3.15 Development Tools and Technologies")
    table("3.7: Development tools and their roles", ["Tool / technology", "Role"], [
        ["HTML / Blade", "Structure of web pages and forms; reusable layouts and components"],
        ["CSS / Tailwind CSS", "Presentation, layout and responsive styling"],
        ["JavaScript / Alpine.js", "Client-side interaction: menus, filters, countdown, dynamic player lists"],
        ["PHP 8.2 / Laravel 12", "Server-side application processing (MVC)"],
        ["MySQL / MariaDB", "Relational database storage"],
        ["Visual Studio Code", "Source-code editor"],
        ["XAMPP", "Local Apache, MariaDB and PHP environment"],
        ["Composer / npm / Vite", "Dependency management and production asset building"],
        ["Pest (PHPUnit)", "Automated testing"],
        ["Chrome / Edge / Firefox", "Browser testing and user access"],
    ], [2.0 * inch, 4.0 * inch])

    h2("3.16 Security and Data Integrity Design")
    table("3.8: Security and integrity measures", ["Threat / risk", "Measure"], [
        ["Unauthorised administrative access", "Authentication plus an <i>admin</i> middleware that checks the user's role on every admin route; form requests re-check authorisation; the role cannot be mass-assigned."],
        ["Password compromise", "Passwords hashed with bcrypt; no default administrator password outside the local environment."],
        ["SQL injection", "All database access through Eloquent / the query builder with bound parameters."],
        ["Cross-site scripting", "Blade escapes output by default; rich text is cleaned by an allow-list HTML sanitiser."],
        ["Cross-site request forgery", "CSRF token on every state-changing form."],
        ["Invalid or tampered data", "Server-side validation of every field and relationship: a match event's team must be in the fixture and its player must belong to that team; schedule conflicts are rejected."],
        ["Orphan or lost records", "Foreign keys with restrict / cascade / set-null rules; soft deletes for teams, players, news and staff."],
        ["Malicious uploads", "Only images within size limits; files re-encoded to WebP under random names, stripping metadata."],
        ["Spam and abuse", "Honeypot field and rate limiting (5 submissions per minute) on public forms."],
        ["Privacy", "Matriculation numbers, state of origin and applicants' contact details are admin-only."],
    ], [1.8 * inch, 4.2 * inch])

    h2("3.17 Testing Strategy")
    P("Testing was performed at unit, module (feature) and integration levels. Automated feature tests drive the application through HTTP requests exactly as a browser would — submitting forms, following redirects and inspecting rendered pages — against a fresh in-memory database for each test, so every test starts from a known state. Each functional requirement is traced to one or more test cases (Section 4.14). Registration was tested with valid and invalid input; team management for creation and listing; fixture management for correct team assignment, conflicts and updates; and score management by entering scores and verifying the correct fixture, standings and statistics. Responsiveness was tested automatically in a headless browser at ten screen widths, and response times of common pages were measured. The user questionnaire (Appendix C) is reserved for post-deployment usability evaluation.")


# =========================================================================== CHAPTER FOUR
def chapter_four(g):
    P, bullets, numbered, h2, h3, chapter, table, screenshot = g["P"], g["bullets"], g["numbered"], g["h2"], g["h3"], g["chapter"], g["table"], g["screenshot"]
    inch = g["inch"]

    chapter("FOUR", "SYSTEM IMPLEMENTATION AND EVALUATION")
    h2("4.1 Introduction")
    P("This chapter describes the implemented system and the results of its evaluation. The system was implemented as <i>BOUESTI Sports</i>, the sports portal of Bamidele Olumilua University of Education, Science and Technology, Ikere-Ekiti. The screenshots were captured from the running system populated with a generated sample season (Section 4.4); all test results and measurements reported were obtained by executing the implemented system.")

    h2("4.2 Implementation Environment")
    table("4.1: Development and test environment", ["Component", "Specification"], [
        ["Hardware", "Intel Core i7-8665U (1.90 GHz), 16 GB RAM"],
        ["Operating system", "Microsoft Windows 11"],
        ["Local server stack", "XAMPP (Apache, MariaDB 10.4.32) and PHP's built-in development server"],
        ["Language / framework", "PHP 8.2.12; Laravel Framework 12"],
        ["Front-end tooling", "Node.js 22, Vite 7, Tailwind CSS 3.4, Alpine.js 3, Trix 2"],
        ["Testing", "Pest 3 on PHPUnit (SQLite in-memory database); headless Microsoft Edge for responsive checks"],
        ["Editor", "Visual Studio Code"],
        ["Client browsers", "Chrome, Edge and Firefox on desktop; mobile emulation for phones and tablets"],
    ], [1.8 * inch, 4.2 * inch])

    h2("4.3 System Structure")
    P("The implemented system comprises 14 Eloquent models, 38 controller classes (public and admin), 17 database migrations, 116 Blade view files, 6 service classes and 121 named routes, of which 71 belong to the administration dashboard; application PHP code amounts to about 5,400 lines. The source follows Laravel's standard folder structure, which separates presentation (resources/views), application logic (app/Http, app/Services), data (app/Models, database/migrations) and configuration.")
    table("4.2: Main modules and their implementation", ["Module", "Key components", "Function"], [
        ["Authentication", "Laravel Breeze controllers, EnsureUserIsAdmin middleware, users.role", "Registration, login, logout, password reset; restriction of the admin dashboard."],
        ["Team and athlete management", "Admin\\TeamController, Admin\\PlayerController, TeamRequest, PlayerRequest", "Teams, squads, player records and statistics."],
        ["Event and fixture management", "Admin\\SeasonController, Admin\\CompetitionController, Admin\\FixtureController, FixtureRequest", "Seasons, competitions and fixtures with conflict checking."],
        ["Score and result management", "FixtureResultRequest, Admin\\MatchEventController, Admin\\LineupController", "Scores, match events, line-ups and result publication."],
        ["Standings and statistics", "LeagueTableService, TeamStatisticsService, PlayerStatisticsService", "Derives standings and statistics from results and events."],
        ["Public information", "HomeController, FixtureController, LeagueTableController, TeamController, PlayerController, NewsController, FrontendController", "Schedules, results, standings, teams, players, news, basketball and facilities."],
        ["Communication", "TrialApplicationController, ContactController and admin inboxes", "Trial applications and contact messages."],
        ["Content and media", "NewsController (admin), RichText sanitiser, ImageOptimizer", "News, gallery and videos."],
        ["Data loading", "FootballImporter (football:import command), SampleDataSeeder", "CSV import of real data; generation of a sample season."],
    ], [1.5 * inch, 2.7 * inch, 1.8 * inch], font_size=9)

    h2("4.4 Database Implementation and Test Data")
    P("The database was created with 17 Laravel migrations that define every table, column type, index, unique constraint and foreign key, so the same schema can be recreated on any server with one command. Seeders create the current season, the league competition, the seven internal teams and the administrator account.")
    P("Because verified squad lists were not available, a deterministic <i>SampleDataSeeder</i> generates a realistic season for testing and demonstration: 126 fictional players (18 per team), 22 fictional staff, and a double round-robin league of 42 fixtures in which matches already due are played with consistent scores, goals, assists, cards, substitutions and line-ups. Real data can replace it through the <i>football:import</i> command (Appendix F).")

    h2("4.5 User Interface Implementation")
    P("The public interface uses a consistent navigation bar with grouped menus (Sports, Club, Team, Fixtures, News, Media, Fans) and a site-wide search. The homepage (Figure 4.1) presents the next fixture with a live countdown, recent results, featured players, news and the university's sports and facilities (Figure 4.2). Fixtures can be filtered by competition, team and month (Figure 4.3); the standings page shows the computed table with each team's form (Figure 4.4); and the match centre shows the score, minute-by-minute events and line-ups (Figure 4.5). The Basketball page (Figure 4.6) presents the university's second sport. Every page adapts to phone screens (Figure 4.7).")
    screenshot(D("doc", "_-1280.png"), "4.1: Homepage")
    screenshot(D("doc", "_home_section0-1280.png"), "4.2: Homepage — sports section")
    screenshot(D("doc", "_fixtures-1280.png"), "4.3: Fixtures and results with competition, team and month filters")
    screenshot(D("doc", "_league_table-1280.png"), "4.4: Standings computed automatically from completed fixtures")
    screenshot(D("doc", "_matches_7-1280.png"), "4.5: Match centre with score and match events")
    screenshot(D("doc", "_basketball-1280.png"), "4.6: Basketball page")
    g["story"].append(g["CondPageBreak"](5 * inch))
    g["figure"](content2.mobile_strip(g), "4.7: Mobile views — homepage, standings, basketball page and navigation menu")

    h2("4.6 Authentication and Authorisation")
    P("Users register with a name, email and confirmed password (Figure 4.8) and log in through the login page (Figure 4.9). Passwords are hashed with bcrypt. Every administrative route passes through the <i>auth</i> and <i>admin</i> middleware; an administrator logging in is taken to the dashboard, an ordinary user to their account page, and an ordinary user who tries an administrative URL receives an HTTP 403 (forbidden) response — hiding a button is never relied upon. Table 4.3 summarises the roles.")
    table("4.3: Roles and permissions", ["Role", "Permissions"], [
        ["Administrator", "Manage seasons, competitions, teams, players, fixtures, scores, match events, line-ups, news, media, staff, trial applications and messages."],
        ["Registered user (participant)", "Log in, manage own profile and password; all public functions."],
        ["Visitor (spectator)", "View schedules, results, standings, teams, players, news and media; apply for trials; send messages."],
    ], [1.9 * inch, 4.1 * inch])
    screenshot(D("doca", "_register-1280.png"), "4.8: User registration page")
    screenshot(D("doca", "_login-1280.png"), "4.9: Login page")

    h2("4.7 Team and Athlete Management")
    P("Teams are listed publicly with their crests (Figure 4.10); each squad is grouped by position (Figure 4.11) and each player has a profile with statistics derived from match events (Figure 4.12). In the dashboard, administrators search and filter players (Figure 4.13) and manage each team's details, squad and record (Figure 4.14). Shirt numbers must be unique within a team. Teams and players that have played are archived (soft-deleted) rather than destroyed, so historical results keep their names. Private data such as matriculation numbers appear only in the dashboard.")
    screenshot(D("doc", "_teams-1280.png"), "4.10: Teams directory")
    screenshot(D("doc", "_squad-1280.png"), "4.11: Squad page with team and position filters")
    screenshot(D("doc", "_players_daniel_arogundade-1280.png"), "4.12: Player profile with statistics")
    screenshot(D("doca", "_admin_players-1280.png"), "4.13: Player management in the administration dashboard")
    screenshot(D("doca", "_admin_teams_amapro_fc-1280.png"), "4.14: Team management — details, squad and record")

    h2("4.8 Event and Fixture Management")
    P("Administrators create seasons (only one can be current) and competitions within them, then schedule fixtures by choosing the competition, home and away teams, date, kick-off time, venue and matchday (Figure 4.15). The form rejects a team playing itself and, through the conflict rule described in Section 3.13.2, a team being scheduled for two active matches on the same day (Figure 4.16). Competitions and seasons that already have fixtures cannot be deleted. Changes to dates, times, venues or status are saved centrally and appear on every public view of the fixture.")
    screenshot(D("doca", "_admin_fixtures-1280.png"), "4.15: Fixture management in the administration dashboard")
    screenshot(D("doca", "_admin_fixture_clash-1280.png"), "4.16: Scheduling conflict detected and rejected")

    h2("4.9 Score and Result Management")
    P("Scores are entered on the fixture's administration page (Figure 4.17). Choosing status “Full time” requires both scores; choosing postponed or cancelled clears any score, so an unfinished match is never published as a result. Goal, assist, card and substitution events are recorded with the minute; once a team is chosen, only that team's players are listed, and the server rejects any event whose team is not in the fixture or whose player belongs to another team. As soon as the result is saved, the public result, the standings, team and player statistics, the homepage and the match centre all reflect it, because all are computed from the same fixture record.")
    screenshot(D("doca", "_admin_fixtures_7-1280.png"), "4.17: Result entry and match events")

    h2("4.10 Administrative Dashboard")
    P("The dashboard (Figure 4.18) is the central management interface. It summarises total teams and players, upcoming and completed matches, active competitions and published news, alerts the administrator to new trial applications and unread messages, and lists upcoming fixtures and recent results with shortcuts to common tasks. The sidebar groups all functions under Football, Matches, Content and People.")
    screenshot(D("doca", "_admin_dashboard-1280.png"), "4.18: Administrative dashboard")

    h2("4.11 Registration of Participants and Communication")
    P("Students apply for team trials through an online form (Figure 4.19) that captures their details, preferred position and experience; applications arrive in an administrative inbox (Figure 4.20) where they are reviewed and given a status. Contact messages follow the same pattern. Organisers publish news with a rich-text editor (Figure 4.21), including match reports linked to fixtures (Figure 4.22).")
    screenshot(D("doc", "_join_the_team-1280.png"), "4.19: Online trial application form")
    screenshot(D("doca", "_admin_trial_applications-1280.png"), "4.20: Trial applications inbox")
    screenshot(D("doca", "_admin_news_create-1280.png"), "4.21: News editor")
    screenshot(D("doc", "_news_amapro_fc_cruise_past_young_boys_fc_in_4_1_win-1280.png"), "4.22: Published match report")

    h2("4.12 System Integration")
    P("Integration occurs because the modules operate through shared data. The complete workflow below was executed end to end as an automated integration test (TC06, TC08 and TC09), confirming that data entered in one module is available to the next:")
    numbered([
        "Create the season and competition (event).",
        "Register participating teams and players.",
        "Create fixtures using valid teams; conflicts are rejected.",
        "The schedule is displayed publicly.",
        "Conduct the match.",
        "Record the score and match events against the correct fixture.",
        "Mark the match full time to confirm the result.",
        "The result, standings and player statistics are displayed to all viewers.",
    ])

    h2("4.13 Test Cases and Results")
    P("Table 4.4 lists the functional test cases defined for the system with their observed results. Every test case was implemented as an automated test and executed with the Pest framework; the date of the final run was 8 October 2026.")
    table("4.4: Functional test cases and results", ["ID", "Test action", "Expected outcome", "Observed outcome", "Status"], [
        ["TC01", "Submit valid user registration", "Account is created and the user is logged in.", "Account stored; user authenticated and redirected to the dashboard.", "Pass"],
        ["TC02", "Submit registration with empty name, invalid email and mismatched passwords", "Submission rejected; fields identified.", "Errors shown for name, email and password; no account created.", "Pass"],
        ["TC03", "Log in with valid credentials", "User reaches the appropriate interface.", "User authenticated and redirected; administrator taken to the admin dashboard.", "Pass"],
        ["TC04", "Log in with an invalid password", "Access denied with an error.", "User remains a guest; error shown.", "Pass"],
        ["TC05", "Administrator creates a team", "Team appears in team records.", "Team stored with slug; offered in the fixture form; listed on the public Teams page.", "Pass"],
        ["TC06", "Create a fixture with valid teams", "Fixture stored and shown in schedule.", "Fixture stored with its season; listed on the public Fixtures page.", "Pass"],
        ["TC07", "Schedule a team for a second active fixture on the same day; schedule a team against itself", "Conflict flagged; nothing saved.", "Both rejected with explanatory messages; allowed on another day or after postponement; editing a fixture does not clash with itself.", "Pass"],
        ["TC08", "Record a 2–1 score and a goal with assist for an existing match", "Correct match score and statistics updated.", "Score stored on the right fixture; scorer has 1 goal, assister 1 assist; incomplete scores and wrong-team players rejected.", "Pass"],
        ["TC09", "Publish a completed result", "Result visible on the results views.", "Result shown on homepage and match centre; standings show the winner first with 3 points; unpublished news stays hidden (HTTP 404).", "Pass"],
        ["TC10", "Unauthorised user opens an admin function", "Protected operation denied.", "Guest redirected to login; ordinary user receives HTTP 403; nothing created.", "Pass"],
        ["TC11", "View schedule and all public pages in the browser", "Pages displayed in readable form.", "All 26 public URLs return HTTP 200 with an empty database and with the full sample season.", "Pass"],
        ["TC12", "Edit an existing fixture", "Updated information reflected in subsequent views.", "New venue and date saved and shown on the public Fixtures page; old venue no longer shown.", "Pass"],
    ], [0.45 * inch, 1.45 * inch, 1.3 * inch, 2.15 * inch, 0.55 * inch], font_size=8.5)
    P("Additional automated tests verified the integrity and security rules of the system (Table 4.5).")
    table("4.5: Additional integrity and security tests", ["ID", "Test", "Observed outcome", "Status"], [
        ["TC13", "Duplicate shirt number in the same team / another team", "Rejected in the same team; allowed across teams", "Pass"],
        ["TC14", "Mark a second season as current", "Only the new season is current", "Pass"],
        ["TC15", "Delete a team that has played; delete a competition with fixtures", "Team archived and history intact; competition deletion refused", "Pass"],
        ["TC16", "Line-up containing a player from another team", "Rejected; valid line-up saved", "Pass"],
        ["TC17", "Upload a PHP file as a team logo; upload a 4000×3000 photo", "PHP file rejected; photo stored as 2000×1500 WebP", "Pass"],
        ["TC18", "Rich text containing script, onerror and javascript: links", "Formatting kept; dangerous content removed", "Pass"],
        ["TC19", "Trial and contact forms: valid, invalid and spam (honeypot) submissions", "Stored; errors shown; spam rejected", "Pass"],
        ["TC20", "CSV import with an invalid row; dry run", "Whole import rolled back with line-numbered errors; dry run saves nothing", "Pass"],
        ["TC21", "Generate sample season", "126 players, 42 fixtures; every score equals its goal events; standings consistent", "Pass"],
    ], [0.5 * inch, 2.4 * inch, 2.5 * inch, 0.6 * inch], font_size=8.8)
    P("In total, <b>96 automated tests with 921 assertions were executed and all passed</b>. They include the tests above and the account tests supplied with Laravel Breeze (password reset, email verification, profile update and deletion), which confirm that standard account functions work correctly. The completed test sheet is in Appendix B.")

    h2("4.14 Requirements Traceability")
    table("4.6: Requirements traceability matrix", ["Requirement", "Implementation module", "Evidence"], [
        ["FR01 User registration", "Breeze registration; trial applications", "TC01, TC02, TC19"],
        ["FR02 Authentication", "Breeze login / logout; admin middleware", "TC03, TC04, TC10"],
        ["FR03 Team management", "Admin\\TeamController", "TC05, TC15, TC17"],
        ["FR04 Player management", "Admin\\PlayerController", "TC08, TC13; Figure 4.13"],
        ["FR05 Event management", "Season and competition controllers", "TC14, TC15"],
        ["FR06 Fixture management", "Admin\\FixtureController, FixtureRequest", "TC06, TC07, TC12; Figure 4.16"],
        ["FR07 Score recording", "Result, match-event and line-up modules", "TC08, TC16"],
        ["FR08 Result publication", "Fixture status; LeagueTableService", "TC09, TC21"],
        ["FR09 Information access", "Public controllers and views", "TC11; Figures 4.1–4.7"],
        ["FR10 Reporting", "Standings, statistics, match centre, dashboard", "TC09, TC21; Figure 4.18"],
        ["FR11 Communication", "Trial and contact modules", "TC19; Figure 4.20"],
        ["FR12 Content", "News, gallery and video modules", "TC17, TC18; Figure 4.21"],
        ["FR13 Data import", "FootballImporter", "TC20"],
    ], [1.8 * inch, 2.4 * inch, 1.8 * inch])

    h2("4.15 Usability Evaluation")
    P("Usability was evaluated against the design principles in Section 2.13 and by automated checks. An automated audit loaded every one of the 27 public pages in headless Microsoft Edge with true mobile emulation at ten widths — 320, 360, 375, 390, 414, 430, 768, 1024, 1280 and 1440 pixels (270 page loads). No page produced horizontal scrolling or overflow at any width; the only text reported below 11 pixels belonged to buttons that deliberately collapse to icons on phones while keeping accessible labels. Interactive elements — mobile menu, search, filters, form validation, the admin sidebar and confirmation dialogs — were exercised in the browser.")
    P("The interface also implements the accessibility practices identified in the literature: labelled form fields with error messages beside the field, keyboard-operable menus with visible focus indicators, text alternatives for images, semantic page landmarks and touch targets of at least 44 pixels on phones.")
    P("The user-satisfaction questionnaire in Appendix C had not been administered at the time of writing. It should be completed by representative organisers, participants and spectators after deployment, and the responses and sample size reported, before conclusions about user satisfaction are drawn.")

    h2("4.16 Performance Evaluation")
    P("Response times of common operations were measured on the environment in Table 4.1 with the sample season loaded, using PHP's single-threaded development server. Each page was requested once to warm up and then ten times; Table 4.7 reports the median and the slowest of the ten requests. The login submission was measured once; it includes deliberate bcrypt password hashing and the redirect to the dashboard.")
    table("4.7: Measured server response times (local environment)", ["Operation", "URL", "Median (ms)", "Slowest (ms)"], [
        ["Homepage", "/", "76", "134"],
        ["Fixtures and results", "/fixtures", "67", "99"],
        ["Standings", "/league-table", "64", "106"],
        ["Match centre", "/matches/7", "72", "85"],
        ["Player profile", "/players/daniel-arogundade", "62", "78"],
        ["Basketball page", "/basketball", "43", "51"],
        ["Login page", "/login", "47", "68"],
        ["Login submission and redirect", "POST /login", "558 (single)", "—"],
        ["Administrative dashboard", "/admin/dashboard", "72", "84"],
        ["Result entry page", "/admin/fixtures/7", "82", "96"],
        ["Player management list", "/admin/players", "74", "97"],
    ], [1.9 * inch, 2.1 * inch, 1.0 * inch, 1.0 * inch])
    P("All common pages were generated in well under the one-second target of the performance requirement. These figures describe the local test environment; times on a production host will additionally depend on the server and network.")

    h2("4.17 Discussion of Findings")
    P("The findings answer the research questions posed in Section 1.5:")
    numbered([
        "<b>Registration and team management (RQ1).</b> Online registration, trial applications and linked team and player records replace scattered lists; validation rejected incomplete and duplicate data (TC01, TC02, TC05, TC13).",
        "<b>Scheduling conflicts and delays (RQ2).</b> Central fixture records with conflict validation prevented a team from being double-booked or drawn against itself (TC07), and edits appeared immediately on every public view (TC12).",
        "<b>Accuracy and accessibility of results (RQ3).</b> Scores are tied to their fixture and validated, and standings and statistics are computed from them, so the table always agrees with the results; the sample-season test confirmed that every score equals its recorded goals and the standings are consistent (TC08, TC09, TC21).",
        "<b>Usability and effectiveness (RQ4).</b> All pages work from 320 to 1440 pixels without overflow, and all functional tests passed. Measured user satisfaction awaits the questionnaire.",
        "<b>Administrative burden (RQ5).</b> Each fact is entered once and every dependent view updates automatically, eliminating manual table computation and repeated announcements. The magnitude of time saved should be measured after deployment.",
    ])
    P("The system has boundaries: it does not include live video streaming, online payment or external federation integration, and basketball is presented as information rather than managed competitions in this version.")


# =========================================================================== CHAPTER FIVE
def chapter_five(g):
    P, bullets, numbered, h2, chapter = g["P"], g["bullets"], g["numbered"], g["h2"], g["chapter"]

    chapter("FIVE", "SUMMARY, CONCLUSION AND RECOMMENDATIONS")
    h2("5.1 Summary")
    P("This project addressed the problem of managing sports events through manual or fragmented administrative processes, in which registration, team management, fixture scheduling, score tracking and result publication become difficult to coordinate as participant and event volumes increase.")
    P("Chapter One established the background, problem, motivation, objectives, significance, scope and limitations. Chapter Two reviewed sports event management as an information-management problem, web-based systems, scheduling, score management, relational databases, three-tier and MVC architecture, frameworks, security and usability. Chapter Three presented the methodology, the analysis of the existing process, the requirements and the design of the system. Chapter Four described the implementation of the system as BOUESTI Sports and reported its evaluation: 96 automated tests with 921 assertions all passed, responsiveness was confirmed on all public pages at ten screen widths, and common pages were served in a median of 43–82 milliseconds locally.")
    h2("5.2 Summary of Findings")
    bullets([
        "A centralised relational database effectively links participant, team, competition, fixture, score, match-event and result information.",
        "Computing standings and statistics from recorded results removes manual calculation errors and keeps every view consistent.",
        "Server-side validation prevents common data errors, including scheduling conflicts and events recorded against the wrong team.",
        "A responsive web interface makes schedules, results and standings accessible to participants and spectators on any device.",
        "Role-based access control and server-side authorisation keep administrative functions and private data away from ordinary users.",
        "Automated testing provides repeatable evidence that each requirement is implemented, and requirements traceability links every objective to its tests.",
        "The system is most appropriate as a focused solution for educational institutions and small organisations rather than a full professional federation platform.",
    ])
    h2("5.3 Conclusion")
    P("The project concludes that a web-based sports event management system is an effective information-system response to the administrative challenges of manual sports administration. All three objectives were achieved: a user-friendly system for registration, scheduling and result tracking was designed (Objective 1); a functional platform enabling organisers to manage competitions, teams and scores, and users to access event information, was implemented and deployed as BOUESTI Sports (Objective 2); and the system's functionality, responsiveness and performance were evaluated, with user-satisfaction evaluation prepared for deployment (Objective 3).")
    P("The work also demonstrates that sports event management software should be designed around the workflow of its users. The most important functions are not isolated pages but connected processes: team information supports fixtures, fixtures support scores, and scores support results and standings. Human decisions — confirming eligibility, resolving disputes and validating official results — remain the responsibility of officials; the system makes those decisions easier to record and communicate.")
    h2("5.4 Recommendations")
    numbered([
        "Institutions adopting the system should train sports organisers and administrators using the user guide (Appendix F) before deployment.",
        "User roles should be clearly defined, and only designated officials should hold administrator accounts.",
        "The system should be hosted on reliable infrastructure with HTTPS and regular automated database backups before being used for important competitions.",
        "A clear procedure should be established for verifying scores before matches are marked full time.",
        "Verified squad data should be imported before launch, and participants' consent obtained before publishing photographs.",
        "The user questionnaire (Appendix C) should be administered after deployment and its results used to guide improvements.",
        "Data-retention and privacy practices for participant information should be documented.",
    ])
    h2("5.5 Contribution to Knowledge")
    P("The project contributes a working, tested model for applying web technologies to small-scale sports event administration. Its contribution is not the claim that digital sports management is new, but the integration of the essential administrative functions — registration, scheduling with conflict detection, score and event recording, derived standings and result publication — into a focused, institution-owned system suitable for an educational context. It also demonstrates requirements traceability from the identified problem through objectives, functional requirements, modules and executed test cases.")
    h2("5.6 Data Quality and Record Integrity")
    P("A system can process information quickly but still produce poor outcomes if the information entered into it is incorrect. The implemented system therefore validates data at several levels: required fields and data types on every form, unique constraints for identifiers such as shirt numbers and email addresses, foreign keys so that a player references a valid team and an event a valid fixture, and cross-checks such as the conflict rule. These controls reduce, but do not eliminate, the need for human verification. An audit log recording who changed a score or fixture, and when, is recommended as a future extension.")
    h2("5.7 Maintenance and Sustainability")
    P("The modular MVC structure, the migrations that define the database, the automated test suite and the installation guide reduce the difficulty of future maintenance. Maintenance should include defect correction, dependency and security updates, database backups and adaptation to new institutional requirements. Sustainability also depends on ownership: the institution should designate responsible administrators, define who may publish results and maintain procedures for correcting inaccurate information.")
    h2("5.8 Suggested Future Work")
    bullets([
        "Full multi-sport management: a sport attribute on competitions, teams and fixtures so that basketball and other sports have fixtures, results and standings, with sport-specific scoring rules.",
        "Automatic fixture generation for round-robin leagues and knockout brackets for cup competitions.",
        "Live score updates and push notifications to subscribed users.",
        "Additional roles, such as team managers who submit their own line-ups for approval, and an audit log of administrative changes.",
        "Online registration payments where institutionally appropriate.",
        "Analytics dashboards and season-to-season comparisons of athlete and team performance.",
        "A mobile application or Progressive Web App with offline access.",
        "Integration with the institution's student information and identity-management systems.",
        "Cloud deployment with automated backups and operational monitoring.",
    ])


# =========================================================================== REFERENCES
def references(g):
    story = g["story"]
    story.append(g["PageBreak"]())
    story.append(g["toc_para"]("REFERENCES", "front", 0))
    refs = [
        "Beck, K. (2002). <i>Test-Driven Development: By Example</i>. Boston: Addison-Wesley.",
        "Codd, E. F. (1970). A relational model of data for large shared data banks. <i>Communications of the ACM</i>, 13(6), 377–387.",
        "Connolly, T., and Begg, C. (2015). <i>Database Systems: A Practical Approach to Design, Implementation, and Management</i> (6th ed.). Harlow: Pearson.",
        "Elmasri, R., and Navathe, S. B. (2016). <i>Fundamentals of Database Systems</i> (7th ed.). Hoboken, NJ: Pearson.",
        "Fielding, R. T. (2000). <i>Architectural Styles and the Design of Network-based Software Architectures</i> (Doctoral dissertation). University of California, Irvine.",
        "Fowler, M. (2002). <i>Patterns of Enterprise Application Architecture</i>. Boston: Addison-Wesley.",
        "Gamma, E., Helm, R., Johnson, R., and Vlissides, J. (1994). <i>Design Patterns: Elements of Reusable Object-Oriented Software</i>. Reading, MA: Addison-Wesley.",
        "Krasner, G. E., and Pope, S. T. (1988). A cookbook for using the model-view-controller user interface paradigm in Smalltalk-80. <i>Journal of Object-Oriented Programming</i>, 1(3), 26–49.",
        "Laravel. (2025). <i>Laravel 12.x Documentation</i>. Retrieved from https://laravel.com/docs/12.x",
        "Laudon, K. C., and Laudon, J. P. (2020). <i>Management Information Systems: Managing the Digital Firm</i> (16th ed.). Harlow: Pearson.",
        "Marcotte, E. (2010, May 25). Responsive web design. <i>A List Apart</i>. Retrieved from https://alistapart.com/article/responsive-web-design/",
        "Nielsen, J. (1993). <i>Usability Engineering</i>. Boston: Academic Press.",
        "Okunbonade, A. A. (2026). <i>Project Proposal: Design and Implementation of a Web-Based Sports Event Management System</i>. Department of Computing and Information Science.",
        "OWASP Foundation. (2021). <i>OWASP Top 10:2021</i>. Retrieved from https://owasp.org/Top10/",
        "Pest. (2025). <i>Pest PHP Testing Framework Documentation</i>. Retrieved from https://pestphp.com/docs",
        "Pressman, R. S., and Maxim, B. R. (2020). <i>Software Engineering: A Practitioner's Approach</i> (9th ed.). New York: McGraw-Hill Education.",
        "Sommerville, I. (2016). <i>Software Engineering</i> (10th ed.). Boston: Pearson.",
        "World Wide Web Consortium (W3C). (2018). <i>Web Content Accessibility Guidelines (WCAG) 2.1</i>. Retrieved from https://www.w3.org/TR/WCAG21/",
    ]
    for r in refs:
        story.append(g["Paragraph"](r, g["styles"]["ref"]))


# =========================================================================== APPENDICES
def appendices(g):
    P, bullets, numbered, table, code, story = g["P"], g["bullets"], g["numbered"], g["table"], g["code"], g["story"]
    inch = g["inch"]

    def appendix(title):
        story.append(g["PageBreak"]())
        story.append(g["toc_para"](title, "front", 0))

    W = [1.6 * inch, 1.35 * inch, 3.05 * inch]
    H = ["Field", "Type", "Description / constraint"]

    appendix("APPENDIX A: DATA DICTIONARY")
    P("Every table also has <i>created_at</i> and <i>updated_at</i> timestamps; soft-deletable tables have <i>deleted_at</i>.")
    table("A.1: users", H, [
        ["id", "BIGINT, PK", "Surrogate key"], ["name", "VARCHAR(255)", "User's name"], ["email", "VARCHAR, unique", "Login identifier"],
        ["password", "VARCHAR", "bcrypt hash — never plain text"], ["role", "VARCHAR(20)", "admin | user (not mass-assignable)"],
    ], W)
    table("A.2: seasons and competitions (events)", H, [
        ["seasons.name, slug", "VARCHAR", "e.g. 2026/2027"], ["seasons.is_current", "BOOLEAN", "Only one season may be current"],
        ["competitions.season_id", "FK to seasons (restrict)", "Owning season"], ["competitions.name, short_name", "VARCHAR", "e.g. BOUESTI Football League / BFL"],
        ["competitions.type", "VARCHAR(20)", "league | cup | friendly | tournament"], ["start_date, end_date, is_active", "DATE / BOOLEAN", ""],
    ], W)
    table("A.3: teams", H, [
        ["id", "BIGINT, PK", ""], ["name, slug", "VARCHAR; unique", "e.g. Amapro FC / amapro-fc"], ["short_name", "VARCHAR(12), null", "Badge text"],
        ["logo", "VARCHAR, null", "Path of optimised image"], ["primary_color, secondary_color", "CHAR(7), null", "Hex colours"],
        ["description, founded_year, captain_name, coach_name", "TEXT / SMALLINT / VARCHAR, null", ""], ["is_active", "BOOLEAN", ""], ["deleted_at", "TIMESTAMP, null", "Soft delete"],
    ], W)
    table("A.4: players", H, [
        ["id", "BIGINT, PK", ""], ["team_id", "FK to teams (restrict)", "Player's team"], ["first_name, last_name, slug", "VARCHAR; slug unique", ""],
        ["jersey_number", "TINYINT, null", "1–99; unique within team"], ["position", "VARCHAR(20)", "goalkeeper | defender | midfielder | forward"],
        ["department, level", "VARCHAR, null", "Public"], ["matric_number, state_of_origin", "VARCHAR, null", "<b>Private</b> — admin only"],
        ["photo, dominant_foot, height, bio", "VARCHAR / TEXT, null", ""], ["is_captain, is_featured, is_active", "BOOLEAN", ""],
    ], W)
    table("A.5: fixtures (matches and results)", H, [
        ["id", "BIGINT, PK", ""], ["competition_id, season_id", "FK (restrict)", "Season follows the competition"],
        ["home_team_id, away_team_id", "FK to teams (restrict)", "Must differ; no same-day clash"], ["match_date, kickoff_time", "DATE, TIME null", ""],
        ["venue, referee", "VARCHAR, null", ""], ["status", "VARCHAR(20)", "scheduled | live | completed | postponed | cancelled"],
        ["home_score, away_score", "TINYINT, null", "Required for live/completed; cleared otherwise"], ["matchday, attendance, featured, report", "INT / BOOLEAN / TEXT", ""],
    ], W)
    table("A.6: match_events and fixture_players", H, [
        ["match_events.fixture_id", "FK (cascade)", ""], ["match_events.team_id", "FK to teams", "Must be a team in the fixture"],
        ["match_events.player_id, related_player_id", "FK to players, null", "Scorer / booked / player off; assister / player on"],
        ["match_events.type", "VARCHAR(20)", "goal | penalty_scored | own_goal | assist | yellow_card | red_card | substitution | penalty_missed"],
        ["match_events.minute, additional_minute", "TINYINT", "e.g. 45 + 2"],
        ["fixture_players.fixture_id, player_id, team_id", "FK; unique (fixture, player)", "Line-up entry"], ["fixture_players.is_starting, is_captain", "BOOLEAN", ""],
    ], W)
    table("A.7: Content and communication tables (summary)", ["Table", "Key fields"], [
        ["news_posts", "title, slug, excerpt, content (sanitised HTML), featured_image, category, author_id, fixture_id, published_at, is_published"],
        ["gallery_items / videos", "title, image or video_url, category, fixture_id, team_id, is_featured"],
        ["staff", "name, slug, photo, role, type (management | coaching), team_id, bio"],
        ["trial_applications", "full_name, email, phone, matric_number, department, level, preferred_position, experience, status, admin_notes"],
        ["contact_messages", "name, email, phone, subject, message, status (new | read | replied | archived)"],
    ], [1.6 * inch, 4.4 * inch])

    appendix("APPENDIX B: COMPLETED SYSTEM TEST SHEET")
    P("Date of final run: 8 October 2026. Tester: automated test suite (Pest 3), executed by the author. Environment: Table 4.1.")
    table("B.1: System test sheet", ["Test ID", "Expected result", "Observed result", "Status"], [
        ["TC01", "Valid registration accepted", "Account created; user logged in", "Pass"],
        ["TC02", "Invalid registration rejected", "Errors for name, email, password; no account", "Pass"],
        ["TC03", "Valid login accepted", "Authenticated; correct interface", "Pass"],
        ["TC04", "Invalid login rejected", "Remains guest; error shown", "Pass"],
        ["TC05", "Team created", "Team stored; available in fixture form; public", "Pass"],
        ["TC06", "Fixture created", "Stored; shown in schedule", "Pass"],
        ["TC07", "Conflict identified", "Same-day clash and self-match rejected", "Pass"],
        ["TC08", "Score recorded correctly", "Correct fixture and statistics updated", "Pass"],
        ["TC09", "Result published", "Shown on result views and standings", "Pass"],
        ["TC10", "Unauthorised action blocked", "Login redirect / HTTP 403", "Pass"],
        ["TC11", "Schedule readable", "All public pages HTTP 200", "Pass"],
        ["TC12", "Fixture edit reflected", "New details shown publicly", "Pass"],
    ], [0.8 * inch, 1.9 * inch, 2.6 * inch, 0.7 * inch])

    appendix("APPENDIX C: PROPOSED USER QUESTIONNAIRE")
    P("The following questionnaire is to be administered after users interact with the deployed system. Actual responses should be collected and analysed before numerical findings are reported. Each statement is answered on the scale: Strongly Agree / Agree / Neutral / Disagree / Strongly Disagree.")
    numbered([
        "I found the system easy to navigate.",
        "The registration process was clear.",
        "The team-management functions were understandable.",
        "The fixture information was easy to find.",
        "The score and result information was easy to understand.",
        "The system reduced the difficulty of obtaining sports-event information.",
        "I would be comfortable using the system during an actual sports event.",
        "The system appears reliable for the core tasks it supports.",
        "The interface is suitable for my device.",
        "Overall, I am satisfied with the system.",
    ])
    P("Open comments: ____________________________________________________________________________")
    P("______________________________________________________________________________________________")

    appendix("APPENDIX D: MODULE SPECIFICATION")
    table("D.1: Module specification", ["Module", "Specification", "Implemented by"], [
        ["Authentication", "Accepts credentials, validates them against stored hashed passwords, creates a session, provides logout, and returns non-revealing error messages.", "Laravel Breeze; EnsureUserIsAdmin"],
        ["Team management", "Create, view, update and archive teams; associate players; preserve history of teams that have played.", "Admin\\TeamController, TeamRequest"],
        ["Player management", "Record athletes and their team; validate mandatory fields and unique shirt numbers; keep private fields admin-only.", "Admin\\PlayerController, PlayerRequest"],
        ["Event", "Seasons and competitions with type, dates and status; foundation for fixtures.", "Season and Competition controllers"],
        ["Fixture", "Create, view, edit and change status of fixtures; reject self-matches and same-day conflicts.", "Admin\\FixtureController, FixtureRequest"],
        ["Score and result", "Scores, match events and line-ups against an existing fixture; result public when full time; corrections recompute everything.", "FixtureResultRequest, MatchEvent and Lineup controllers"],
        ["Reporting", "Schedules, results, standings, statistics, match centre and dashboard summaries.", "LeagueTableService, statistics services, public controllers"],
    ], [1.2 * inch, 3.0 * inch, 1.8 * inch], font_size=9)

    appendix("APPENDIX E: ACCEPTANCE CRITERIA")
    table("E.1: Acceptance criteria and status", ["Criterion", "Status (evidence)"], [
        ["Registration is accepted when all mandatory fields are valid, and the record can be retrieved.", "Met (TC01)"],
        ["An invalid registration is rejected with a message explaining the correction required.", "Met (TC02)"],
        ["An administrator can create a team and then select it when creating a fixture.", "Met (TC05)"],
        ["A fixture cannot be saved with an invalid team reference or a scheduling conflict.", "Met (TC07)"],
        ["A schedule update is reflected wherever the fixture is displayed.", "Met (TC12)"],
        ["A score entered for one match is not displayed as the score of another match.", "Met (TC08)"],
        ["An incomplete result is not shown as an official public result.", "Met (TC08, TC09)"],
        ["An ordinary viewer cannot perform administrative create, update or delete operations.", "Met (TC10)"],
        ["Core pages remain readable on desktop and mobile-sized browser windows.", "Met (Section 4.15)"],
        ["Database records remain available after the application is restarted.", "Met (persistent MySQL database)"],
    ], [4.4 * inch, 1.6 * inch])

    appendix("APPENDIX F: USER AND INSTALLATION GUIDE")
    P("<b>F.1 Visitors and participants.</b> Open the website in any browser. Use the menu (the menu button on phones) to reach Sports, Club, Team, Fixtures, News, Media and Fans pages, and the search button to find any page. On the Fixtures page, switch between Upcoming and Results and filter by competition, team or month; select <i>Match Centre</i> for a match's events and line-ups. To create an account, choose Register; to apply for a team, open <i>Join the Club</i>.")
    P("<b>F.2 Administrators.</b> Log in at <i>/login</i>; you are taken to <i>/admin/dashboard</i>.")
    numbered([
        "Seasons &gt; New season (tick <i>Current season</i>); Competitions &gt; New competition (type League for standings).",
        "Teams: add or edit each team; Players &gt; Add player, choosing the team and position.",
        "Fixtures &gt; New fixture: choose competition, teams, date, kick-off time and venue. A team cannot be booked twice on the same day.",
        "After the match, open the fixture, choose <i>Full time</i>, enter both scores and save — standings update immediately. Then add match events and tick the line-ups.",
        "Use News, Gallery and Videos for content, and Trial Applications and Messages for the inboxes. Deleting asks for confirmation; teams and players are archived, not destroyed.",
    ])
    P("<b>F.3 Installation.</b> Requirements: PHP 8.2+ with GD and PDO MySQL extensions, Composer 2, Node.js 18+, MySQL 8 or MariaDB 10.4+.")
    story.append(g["Preformatted"]("""composer install --no-dev --optimize-autoloader
npm install && npm run build
cp .env.example .env && php artisan key:generate
#   edit .env: APP_URL, DB_*, ADMIN_EMAIL, ADMIN_PASSWORD, MAIL_*, SEED_SAMPLE_DATA=false
php artisan migrate --seed       # season, league, teams, administrator
php artisan storage:link
php artisan football:import      # load verified CSV data (use --dry-run first)
php artisan test                 # run the automated tests""", g["styles"]["code"]))

    appendix("APPENDIX G: SELECTED SOURCE CODE")
    code("app/Http/Middleware/EnsureUserIsAdmin.php", title="G.1 Administrator access middleware")
    code("app/Http/Requests/Admin/FixtureRequest.php", title="G.2 Fixture validation, including the scheduling-conflict rule")
    code("app/Services/LeagueTableService.php", title="G.3 Standings computation")
    code("app/Http/Requests/Admin/MatchEventRequest.php", title="G.4 Validation of match events against the fixture's teams")
