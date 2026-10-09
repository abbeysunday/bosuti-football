# -*- coding: utf-8 -*-
"""Front matter, Chapter One and Chapter Two of the project report."""
import content2

STUDENT_NAME = "[STUDENT FULL NAME]"
MATRIC = "[MATRIC NUMBER]"
SUPERVISOR = "[SUPERVISOR'S NAME AND TITLE]"
HOD = "[HEAD OF DEPARTMENT'S NAME AND TITLE]"
MONTH_YEAR = "OCTOBER, 2026"
TITLE = "DESIGN AND IMPLEMENTATION OF A WEB-BASED SPORTS MANAGEMENT AND INFORMATION PORTAL FOR INTERNAL STUDENT COMPETITIONS (A CASE STUDY OF BAMIDELE OLUMILUA UNIVERSITY OF EDUCATION, SCIENCE AND TECHNOLOGY, IKERE-EKITI)"


def build(g):
    story = g["story"]
    P, bullets, numbered, h2, h3, chapter, front_heading = g["P"], g["bullets"], g["numbered"], g["h2"], g["h3"], g["chapter"], g["front_heading"]
    table, figure = g["table"], g["figure"]
    S = g["styles"]
    Paragraph, Spacer, PageBreak, NextPageTemplate = g["Paragraph"], g["Spacer"], g["PageBreak"], g["NextPageTemplate"]
    inch = g["inch"]

    # ------------------------------------------------------------------ chapters first
    # (built first so the lists of figures/tables know their entries; front matter is prepended below)
    chapter_one(g)
    chapter_two(g)
    content2.build(g)
    body = list(story)
    story.clear()

    # ------------------------------------------------------------------ title page
    story.append(NextPageTemplate("cover"))
    story.append(Spacer(1, 0.6 * inch))
    crest = g["Image"](g["os"].path.join(g["PROJECT"], "public", "frontend", "assets", "images", "bouesti-fc-crest.png"), width=1.25 * inch, height=1.25 * inch, kind="proportional")
    story.append(crest)
    story.append(Spacer(1, 0.3 * inch))
    P(f"<b>{TITLE}</b>", "center")
    story.append(Spacer(1, 0.45 * inch))
    P("BY", "center")
    story.append(Spacer(1, 0.15 * inch))
    P(f"<b>{STUDENT_NAME}</b>", "center")
    P(f"<b>{MATRIC}</b>", "center")
    story.append(Spacer(1, 0.45 * inch))
    P("A PROJECT SUBMITTED TO THE DEPARTMENT OF COMPUTER SCIENCE, FACULTY OF SCIENCE, BAMIDELE OLUMILUA UNIVERSITY OF EDUCATION, SCIENCE AND TECHNOLOGY, IKERE-EKITI, EKITI STATE, NIGERIA", "center")
    story.append(Spacer(1, 0.25 * inch))
    P("IN PARTIAL FULFILMENT OF THE REQUIREMENTS FOR THE AWARD OF THE DEGREE OF BACHELOR OF SCIENCE (B.Sc.) IN COMPUTER SCIENCE", "center")
    story.append(Spacer(1, 0.45 * inch))
    P(f"<b>{MONTH_YEAR}</b>", "center")

    # ------------------------------------------------------------------ preliminaries (roman numerals)
    story.append(NextPageTemplate("front"))
    front_heading("CERTIFICATION")
    P(f"This is to certify that this project, titled <i>“Design and Implementation of a Web-Based Sports Management and Information Portal for Internal Student Competitions (A Case Study of Bamidele Olumilua University of Education, Science and Technology, Ikere-Ekiti)”</i>, was carried out by <b>{STUDENT_NAME}</b> with matriculation number <b>{MATRIC}</b> in the Department of Computer Science, Faculty of Science, Bamidele Olumilua University of Education, Science and Technology, Ikere-Ekiti, under my supervision.")
    story.append(Spacer(1, 0.6 * inch))
    sig = [["_______________________________", "_______________________________"],
           [SUPERVISOR, "Date"], ["Project Supervisor", ""], ["", ""], ["", ""],
           ["_______________________________", "_______________________________"],
           [HOD, "Date"], ["Head of Department", ""], ["", ""], ["", ""],
           ["_______________________________", "_______________________________"],
           ["External Examiner", "Date"]]
    t = g["Table"](sig, colWidths=[3.3 * inch, 2.6 * inch])
    t.setStyle(g["TableStyle"]([("FONTNAME", (0, 0), (-1, -1), "Times-Roman"), ("FONTSIZE", (0, 0), (-1, -1), 11.5)]))
    story.append(t)

    front_heading("DEDICATION")
    P("[This project is dedicated to ... — write your personal dedication here, for example to God Almighty and to your parents or guardians.]", "center")

    front_heading("ACKNOWLEDGEMENTS")
    P(f"[Write your personal acknowledgements here. Conventionally, the student thanks God, the project supervisor ({SUPERVISOR}) for guidance throughout the work, the Head of Department and lecturers of the Department of Computer Science, family members for their support, and colleagues and friends — including the players, coaches and organisers of the internal football and basketball teams — who contributed information during the study.]")

    front_heading("ABSTRACT")
    P("Student sport — above all the internal football competitions between student clubs, and increasingly basketball — is an important part of campus life at Bamidele Olumilua University of Education, Science and Technology (BOUESTI), Ikere-Ekiti. Information about these activities — fixtures, results, league standings, squads, facilities and news — is, however, largely circulated informally, which makes it difficult for students to follow campus sport and for organisers to keep accurate, verifiable records. This project designed and implemented BOUESTI Sports, a web-based sports management and information portal for the university, with full league management for internal football as its core module and a dedicated basketball section as the first step towards multi-sport coverage. The system was developed with the Laravel 12 framework (PHP 8.2) following the Model-View-Controller (MVC) architectural pattern, a MariaDB/MySQL relational database, Blade templates, Tailwind CSS and Alpine.js, and was engineered using an iterative and incremental development methodology. It provides a responsive public website where visitors can explore the university's sports and its real playing facilities, view football fixtures and results, a league table that is computed automatically from completed matches, team and player profiles with statistics derived from recorded match events, a match centre with minute-by-minute events and line-ups, news, a photo gallery and videos; students can also apply for trials and send enquiries online. A role-protected administration panel allows authorised officials to manage seasons, competitions, teams, players, fixtures, results, match events, line-ups, news (through a sanitised rich-text editor), media, staff, trial applications and contact messages, while a CSV import tool supports bulk loading of real squad and fixture data. The system was verified with 96 automated feature and unit tests (921 assertions) covering the core workflow, validation, security, data integrity and rendering, and was checked for responsiveness at ten screen widths from 320 to 1440 pixels across all 27 public pages. The results show that the portal eliminates manual computation of standings, preserves historical match data, protects students' private information, and gives the university sports community a single, reliable and accessible source of information.")
    P("<b>Keywords:</b> sports information system, football league management, web application, Laravel, MVC, relational database, responsive web design.", "bodyleft")

    front_heading("TABLE OF CONTENTS", toc=False)
    toc = g["TableOfContents"](formatter=g["page_label"])
    toc.levelStyles = [S["toc1"], S["toc2"]]
    toc.dotsMinLevel = 0
    story.append(toc)

    front_heading("LIST OF TABLES")
    lot = g["ListOf"]("TabEntry", formatter=g["page_label"])
    lot.levelStyles = [S["toc2"]]
    story.append(lot)

    front_heading("LIST OF FIGURES")
    lof = g["ListOf"]("FigEntry", formatter=g["page_label"])
    lof.levelStyles = [S["toc2"]]
    story.append(lof)

    front_heading("LIST OF ABBREVIATIONS")
    table("0.1: Abbreviations used in this report", ["Abbreviation", "Meaning"], [
        ["BOUESTI", "Bamidele Olumilua University of Education, Science and Technology"],
        ["CRUD", "Create, Read, Update and Delete"], ["CSRF", "Cross-Site Request Forgery"],
        ["CSS", "Cascading Style Sheets"], ["CSV", "Comma-Separated Values"], ["DFD", "Data Flow Diagram"],
        ["ERD", "Entity-Relationship Diagram"], ["GD", "Graphics Draw (PHP image library)"],
        ["HTML", "HyperText Markup Language"], ["HTTP", "HyperText Transfer Protocol"],
        ["MVC", "Model-View-Controller"], ["ORM", "Object-Relational Mapping"],
        ["OWASP", "Open Worldwide Application Security Project"], ["PHP", "PHP: Hypertext Preprocessor"],
        ["RDBMS", "Relational Database Management System"], ["SQL", "Structured Query Language"],
        ["UI / UX", "User Interface / User Experience"], ["URL", "Uniform Resource Locator"],
        ["WCAG", "Web Content Accessibility Guidelines"], ["XSS", "Cross-Site Scripting"],
    ], [1.5 * inch, 4.5 * inch])

    story.extend(body)


# =========================================================================== CHAPTER ONE
def chapter_one(g):
    P, bullets, numbered, h2, h3, chapter, table = g["P"], g["bullets"], g["numbered"], g["h2"], g["h3"], g["chapter"], g["table"]
    inch = g["inch"]

    chapter("ONE", "INTRODUCTION")
    h2("1.1 Background to the Study")
    P("Football is the most widely played and followed sport in Nigeria, and universities are no exception; basketball, athletics and other sports are also played widely on Nigerian campuses. Beyond inter-university competitions, many Nigerian universities host internal competitions in which student-organised clubs — often formed around departments, faculties, halls of residence or groups of friends — compete against each other throughout the academic session. These competitions promote physical fitness, discipline, teamwork and social cohesion, and they provide a pathway for talented students to be identified for the institution's representative teams.")
    P("At Bamidele Olumilua University of Education, Science and Technology (BOUESTI), Ikere-Ekiti, several student football clubs compete internally. The clubs covered by this study are Amapro FC, Elite FC, Young Boys FC, CSC Elites, Engines Boys, Sovereignty FC and AMCOMS. Alongside football, students at the university play basketball on its outdoor courts, and the university's sports grounds — a football pitch with a covered grandstand and a set of hard courts — serve all of these activities. A football competition of this kind produces a steady stream of information: fixture schedules, venues and kick-off times, match results, goal scorers and disciplinary records, league standings, squad lists, trial announcements and club news.")
    P("Modern web technologies make it possible to manage this information in a structured database and to publish it instantly to any device with a browser. Professional football competitions routinely provide official websites with live standings, match centres and player statistics. The same capability, adapted to the scale and needs of a university, can transform how internal competitions are organised and followed. This project applies established software engineering practice — requirements analysis, relational database design, the Model-View-Controller architecture and automated testing — to build such a platform for BOUESTI's student sport, branded <i>BOUESTI Sports</i>: a complete management system for the internal football competitions, together with a basketball section and a presentation of the university's sports facilities, structured so that further sports can be added.")

    h2("1.2 Statement of the Problem")
    P("Information about internal sports competitions — football in particular — is typically managed and shared informally — through notice boards, word of mouth, messaging-app groups and individual record books kept by organisers. This approach gives rise to the following problems:")
    numbered([
        "<b>Fragmented and inaccessible information.</b> There is no single, authoritative place where a student can see the fixture list, the latest results, the league table or a team's squad. Messages in chat groups are quickly buried and are not accessible to students outside those groups.",
        "<b>Error-prone manual computation of standings.</b> League tables computed by hand from paper records are slow to update and prone to arithmetic errors in points, goal difference and ordering, which can lead to disputes.",
        "<b>Poor record keeping and loss of history.</b> Results, scorers and disciplinary records of previous matches and seasons are easily lost, so player achievements and club history cannot be verified.",
        "<b>No recognition of individual players.</b> Without a structured record of match events, individual statistics such as goals, assists and appearances cannot be produced, and talented players receive little visibility.",
        "<b>Inefficient recruitment and communication.</b> Students who want to join a team have no formal channel to apply for trials, and enquiries to organisers are handled ad hoc.",
        "<b>Low visibility of other sports and facilities.</b> Students who are not already involved have no easy way to learn which sports are available — for example basketball — or what playing facilities the university has.",
        "<b>Privacy risks.</b> When student information is shared informally, sensitive details such as matriculation numbers and phone numbers may be exposed to people who should not see them.",
    ])

    h2("1.3 Aim and Objectives of the Study")
    P("The <b>aim</b> of this project is to design and implement a secure, responsive, web-based sports management and information portal for BOUESTI, with full league management for the internal student football competitions and a structure that presents other sports, beginning with basketball.")
    P("The specific <b>objectives</b> are to:")
    numbered([
        "analyse the information requirements of the internal football competition and design a normalised relational database for seasons, competitions, teams, players, fixtures, match events, line-ups, news, media, staff, trial applications and enquiries;",
        "develop a public, mobile-responsive website that presents the university's sports (football and basketball) and its sports facilities, together with fixtures, results, a match centre, team and player profiles, news, a gallery and videos;",
        "implement an algorithm that computes league standings automatically from completed fixtures, and services that derive player and team statistics from recorded match events;",
        "develop a role-based administration panel through which authorised officials manage all competition data and website content;",
        "provide online channels for trial applications and contact enquiries, with an administrative inbox;",
        "secure the system against common web vulnerabilities and protect students' private data; and",
        "test the system with automated tests and evaluate it against the stated requirements.",
    ])

    h2("1.4 Scope of the Study")
    P("The study covers student sport at BOUESTI. Its core is the internal football competitions played between student clubs, for which the system manages multiple seasons and competitions (league, cup, friendly and tournament types), the seven internal teams and their players, the full match lifecycle (scheduling, live and completed statuses, postponement and cancellation), match events (goals, penalties, own goals, assists, cards and substitutions), line-ups, automatically computed standings and statistics, news, a gallery, videos, management and coaching staff, trial applications and contact messages. Bulk import of real data from spreadsheets and optimisation of uploaded images are included. The portal also includes a basketball section (programme, courts and how to get involved) and a showcase of the university's real sports facilities — the football pitch and grandstand and the outdoor hard courts.")
    P("The following are outside the scope of this version: live minute-by-minute commentary pushed in real time, ticketing and online payment, fantasy football, betting, player transfers between clubs, native mobile applications and representation of inter-university competitions. Basketball is presented as information only in this version: basketball fixtures, results, standings and statistics are not yet managed in the database and are recommended for future work (Section 5.5).")

    h2("1.5 Significance of the Study")
    bullets([
        "<b>Students and supporters</b> gain a single, always-available source of accurate fixtures, results, standings and news on any device, and can discover the other sports and facilities available to them.",
        "<b>Players</b> receive recognition through public profiles and statistics built from verified match records.",
        "<b>Competition organisers and the university sports unit</b> save time: results entered once automatically update standings, statistics and the homepage, and historical records are preserved.",
        "<b>Aspiring players</b> have a formal, transparent channel for applying for trials.",
        "<b>The university</b> gains a professional digital presence for campus sport that promotes student engagement and wellbeing.",
        "<b>Academically</b>, the project demonstrates the application of software engineering, database design, web security and testing principles to a real campus problem, and can serve as a reference for similar systems.",
    ])

    h2("1.6 Limitations of the Study")
    bullets([
        "<b>Data availability.</b> Official squad lists, staff details and historical results were not available in machine-readable form during development. The system was therefore demonstrated and tested with a generated, clearly labelled sample football season of fictional players and staff; a CSV import tool is provided so that verified data can be loaded without code changes.",
        "<b>Connectivity.</b> Users need internet access to reach the portal; it does not work offline.",
        "<b>Human input.</b> The accuracy of statistics depends on officials recording results and match events correctly and promptly.",
        "<b>Time and resources</b> limited the project to a web application; features such as push notifications and native mobile apps are recommended for future work.",
    ])

    h2("1.7 Definition of Terms")
    table("1.1: Definition of terms", ["Term", "Definition"], [
        ["Fixture", "A scheduled match between a home team and an away team in a competition."],
        ["Matchday", "A round of a competition in which each team plays at most one match."],
        ["Standings / league table", "The ranking of teams in a competition by points, goal difference and goals scored."],
        ["Goal difference (GD)", "Goals scored (GF) minus goals conceded (GA)."],
        ["Match event", "A recorded incident in a match: goal, penalty, own goal, assist, yellow or red card, or substitution."],
        ["Line-up", "The players named for a match: the starting eleven and the substitutes."],
        ["Clean sheet", "A match in which a team (and its starting goalkeeper) concedes no goals."],
        ["Framework", "A reusable software platform that provides structure and common services for building applications."],
        ["Soft delete", "Marking a record as deleted without physically removing it, so that historical references remain valid."],
        ["Responsive design", "A web design approach in which layouts adapt to the size of the user's screen."],
        ["Multi-sport portal", "A single website that presents more than one sport under one brand, with shared navigation and design."],
    ], [1.8 * inch, 4.2 * inch])

    h2("1.8 Organisation of the Report")
    P("Chapter One introduces the study. Chapter Two reviews literature on sports information systems, web application architecture, databases, security and usability. Chapter Three presents the analysis of the existing system, the requirements and the design of the proposed system, including its architecture, database and algorithms. Chapter Four describes the implementation, testing and results. Chapter Five summarises the work and presents conclusions and recommendations. The appendices contain a user manual, an installation guide and selected source code.")


# =========================================================================== CHAPTER TWO
def chapter_two(g):
    P, bullets, h2, h3, chapter, table = g["P"], g["bullets"], g["h2"], g["h3"], g["chapter"], g["table"]
    inch = g["inch"]

    chapter("TWO", "LITERATURE REVIEW")
    h2("2.1 Introduction")
    P("This chapter reviews the concepts, technologies and existing approaches relevant to the development of a sports management and information portal centred on football league management. It covers information systems and sports information systems, web application architecture and the Model-View-Controller pattern, web frameworks, relational databases, responsive and accessible design, web application security and software testing, and it concludes with a review of existing approaches and the gap this project fills.")

    h2("2.2 Information Systems")
    P("An information system is an organised combination of people, procedures, data, software and hardware that collects, processes, stores and distributes information to support decision making and operations. Sommerville (2016) describes such systems as socio-technical systems in which the software component must be understood in relation to the organisation that uses it. For a competition, the raw data are fixtures, results and match events; the information derived from them — standings, statistics and reports — is what users actually need. A well-designed information system captures each fact once, at its source, and derives all other information from it automatically, which is the principle adopted in this project: officials record results and events once, and the league table and player statistics are computed rather than typed.")

    h2("2.3 Sports Information and League Management Systems")
    P("Sports information systems support the administration of competitions and the publication of information to participants and the public. Their typical functions are: registration of teams and players; generation and publication of fixture schedules; capture of results and match events; computation of standings; production of individual and team statistics; and communication through news and media. At the professional level these functions are provided by official competition websites with match centres, live tables and detailed statistics. At the grassroots and amateur level, commercial software-as-a-service platforms (for example, TeamSnap and LeagueApps) offer scheduling, registration and communication features on a subscription basis.")
    P("For a university's internal competitions these general solutions have drawbacks: they are designed around foreign clubs and payment models, they are not branded to the institution, they do not model student-specific data such as departments, levels and matriculation numbers, and they may store student data on third-party services outside the institution's control. A purpose-built system, owned and hosted by the institution, can reflect the structure of campus competitions exactly and apply appropriate privacy rules.")
    P("Universities and sports bodies commonly present all of their sports under a single brand — one website with a section per sport — rather than separate sites. This gives students one place to find every sporting opportunity, lets sports share common features such as news, media and contact channels, and allows less established sports to benefit from the audience of the most popular one. The portal developed in this project follows this pattern: football, the most organised campus competition, is fully managed, while basketball is presented within the same site and navigation.")

    h3("2.3.1 League standings")
    P("Most association football leagues rank teams by awarding three points for a win, one for a draw and none for a defeat, and break ties first by goal difference and then by goals scored. Standings are a pure function of the set of completed results: given the same results, the table is always the same. This property means that standings should not be stored and edited manually but computed from the results, which removes a whole class of inconsistency errors — a design decision central to this project (Section 3.10).")

    h2("2.4 Web Applications and the Client-Server Model")
    P("A web application is software whose user interface runs in a web browser and which communicates with a server over the HyperText Transfer Protocol (HTTP). Fielding (2000) formalised the architectural principles of the web, including the stateless client-server interaction on which web applications are built. Web applications need no installation on the user's device, are updated centrally and are reachable from any device with a browser — properties that make them well suited to publishing competition information to a large and varied student audience.")

    h2("2.5 The Model-View-Controller (MVC) Architecture")
    P("The Model-View-Controller pattern, first described for Smalltalk-80 user interfaces (Krasner and Pope, 1988), separates an application into three responsibilities: the <b>Model</b>, which represents data and business rules; the <b>View</b>, which presents data to the user; and the <b>Controller</b>, which receives user input, coordinates the model and selects the view. This separation of concerns improves maintainability, testability and the ability of different people to work on different parts of the system (Gamma et al., 1994; Fowler, 2002). Server-side web frameworks such as Laravel adopt MVC: HTTP requests are routed to controllers, controllers use models to read and write the database, and responses are rendered from view templates.")

    h2("2.6 Web Development Frameworks and Laravel")
    P("A web framework provides the common infrastructure required by most web applications — routing, request handling, database access, templating, authentication, validation and security protections — so that developers can concentrate on application-specific logic. Laravel is an open-source PHP framework that follows the MVC pattern and provides, among other features, the Eloquent object-relational mapper (an implementation of the Active Record pattern described by Fowler, 2002), database migrations and seeders, the Blade templating engine, form request validation, middleware, authentication scaffolding, file storage abstraction and first-class support for automated testing (Laravel, 2025). These features, together with its large community and documentation, made Laravel the chosen framework for this project (Section 3.4).")

    h2("2.7 Relational Databases")
    P("The relational model, introduced by Codd (1970), represents data as relations (tables) of tuples (rows) with attributes (columns), related to one another through keys. Relational database management systems (RDBMS) such as MySQL and MariaDB implement this model and use Structured Query Language (SQL). Normalisation reduces redundancy and update anomalies by ensuring that each fact is stored once (Elmasri and Navathe, 2016; Connolly and Begg, 2015). Referential integrity, enforced through foreign-key constraints, guarantees that relationships between records remain valid — for example, that every match event belongs to an existing fixture. Competition data is highly structured and relational (a fixture relates two teams; an event relates a fixture, a team and players), which makes an RDBMS the appropriate storage technology.")

    h2("2.8 Responsive and Accessible Web Design")
    P("Students access the web predominantly from mobile phones. Responsive web design, articulated by Marcotte (2010), uses fluid layouts, flexible images and CSS media queries so that a single site adapts to screens of any size. Accessibility ensures that people with disabilities can perceive, operate and understand web content; the World Wide Web Consortium's Web Content Accessibility Guidelines (WCAG) 2.1 define testable criteria such as sufficient colour contrast, keyboard operability, visible focus indicators and text alternatives for images (W3C, 2018). Usability research emphasises consistency, visibility of system status, error prevention and clear error messages (Nielsen, 1993). These principles guided the user-interface work described in Chapter Four.")

    h2("2.9 Web Application Security")
    P("Web applications are exposed to attack from anyone on the internet. The OWASP Top 10 (OWASP Foundation, 2021) lists the most critical web application security risks, including broken access control, injection, cryptographic failures, insecure design and security misconfiguration. Established countermeasures relevant to this project are: role-based access control enforced on the server; parameterised queries (provided by an ORM) to prevent SQL injection; output escaping and HTML sanitisation to prevent cross-site scripting (XSS); anti-forgery tokens to prevent cross-site request forgery (CSRF); secure password hashing; validation of all input, including uploaded files; and rate limiting of public forms. Privacy regulations and good practice additionally require that personal data be collected only for a purpose and exposed only to those who need it.")

    h2("2.10 Software Development Methodologies")
    P("Pressman and Maxim (2020) and Sommerville (2016) describe several software process models. The waterfall model proceeds through requirements, design, implementation, testing and maintenance sequentially; it is simple to manage but inflexible when requirements change. Iterative and incremental models deliver the system in successive increments, each adding functionality and each refined through feedback; agile methods emphasise working software, collaboration with users and responsiveness to change. Because the requirements of this project were refined progressively (the user interface was built first, then the data model, then administration, then supporting features), an iterative and incremental approach was adopted (Section 3.2).")

    h2("2.11 Software Testing")
    P("Testing provides evidence that software meets its requirements and reveals defects. Unit tests verify individual components in isolation; feature (integration) tests verify that components work together — for example, that submitting a form stores a record and changes a page; system and acceptance tests verify the whole system against user requirements (Sommerville, 2016). Automated tests can be re-run after every change to detect regressions, a practice central to test-driven development (Beck, 2002). This project uses the Pest testing framework on top of PHPUnit to automate feature tests that exercise the application through HTTP requests against an isolated test database.")

    h2("2.12 Review of Existing Approaches and Research Gap")
    table("2.1: Comparison of approaches to managing internal competition information", ["Approach", "Strengths", "Weaknesses"], [
        ["Notice boards and word of mouth", "No cost; familiar", "Limited reach; slow; no history; no statistics"],
        ["Messaging-app groups", "Fast; free; widely used", "Information buried in chat; restricted membership; no structure; privacy risks"],
        ["Spreadsheets kept by organisers", "Structured; can compute tables", "Single user; manual errors; not published; hard to share securely"],
        ["Commercial league platforms", "Rich features; hosted", "Subscription cost; not institution-branded; no student-specific data; data held by third parties"],
        ["<b>Proposed portal (this project)</b>", "Single source of truth; automatic standings and statistics; responsive public site; role-based admin; institution-owned; privacy by design", "Requires hosting and trained officials to enter data"],
    ], [1.7 * inch, 2.15 * inch, 2.15 * inch])
    P("The review shows that informal approaches cannot provide accurate, accessible and historical information, and general commercial tools do not fit the structure, branding and privacy needs of a university's internal competitions. The gap addressed by this project is a purpose-built, institution-owned sports portal that stores each competition fact once, derives standings and statistics automatically, publishes them on a responsive and accessible website, and protects student data through role-based access control.")
