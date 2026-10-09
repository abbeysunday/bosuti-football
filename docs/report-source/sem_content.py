# -*- coding: utf-8 -*-
"""Sports Event Management System report: front matter, Chapter One and Chapter Two.

Build with:  python build_doc.py sem
Chapters Three to Five, references and appendices are in sem_content2.py.
"""
import sem_content2

STUDENT_NAME = "OKUNBONADE ABIODUN AYOMIDE"
MATRIC = "5095"
SUPERVISOR = "[SUPERVISOR'S NAME AND TITLE]"
HOD = "[HEAD OF DEPARTMENT'S NAME AND TITLE]"
DEPARTMENT = "DEPARTMENT OF COMPUTING AND INFORMATION SCIENCE"
MONTH_YEAR = "OCTOBER, 2026"
TITLE = "DESIGN AND IMPLEMENTATION OF A WEB-BASED SPORTS EVENT MANAGEMENT SYSTEM"
TITLE_CASE = "Design and Implementation of a Web-Based Sports Event Management System"
OUT_FILE = "Web_Based_Sports_Event_Management_System_Report.pdf"
PDF_TITLE = TITLE_CASE


def build(g):
    story = g["story"]
    P, front_heading = g["P"], g["front_heading"]
    Spacer, NextPageTemplate = g["Spacer"], g["NextPageTemplate"]
    S, inch, table = g["styles"], g["inch"], g["table"]

    # chapters first, so the lists of figures/tables know their entries; front matter is prepended below
    chapter_one(g)
    chapter_two(g)
    sem_content2.build(g)
    body = list(story)
    story.clear()

    # ------------------------------------------------------------------ title page
    story.append(NextPageTemplate("cover"))
    story.append(Spacer(1, 0.5 * inch))
    P(f"<b>{DEPARTMENT}</b>", "center")
    P("<b>COURSE OF STUDY: COMPUTER SCIENCE</b>", "center")
    story.append(Spacer(1, 0.6 * inch))
    P(f"<b>{TITLE}</b>", "center")
    story.append(Spacer(1, 0.6 * inch))
    P(f"A PROJECT REPORT SUBMITTED TO THE {DEPARTMENT} IN PARTIAL FULFILMENT OF THE REQUIREMENTS FOR THE AWARD OF A DEGREE IN COMPUTER SCIENCE", "center")
    story.append(Spacer(1, 0.5 * inch))
    P("BY", "center")
    story.append(Spacer(1, 0.15 * inch))
    P(f"<b>{STUDENT_NAME}</b>", "center")
    P(f"<b>MATRIC NUMBER: {MATRIC}</b>", "center")
    story.append(Spacer(1, 0.6 * inch))
    P(f"<b>{MONTH_YEAR}</b>", "center")

    # ------------------------------------------------------------------ preliminaries
    story.append(NextPageTemplate("front"))
    front_heading("CERTIFICATION")
    P(f"This is to certify that this project report, titled <i>“{TITLE_CASE}”</i>, was carried out by <b>{STUDENT_NAME}</b> (Matric Number: <b>{MATRIC}</b>) in the Department of Computing and Information Science. The report presents the analysis, design, implementation and evaluation of a web-based platform for the organisation, scheduling, score tracking and publication of sports events, and it has been read and approved as meeting the requirements for the award of a degree in Computer Science.")
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

    front_heading("DECLARATION")
    P(f"I, <b>{STUDENT_NAME}</b>, hereby declare that this project report, titled <i>“{TITLE_CASE}”</i>, is a record of work carried out by me in the Department of Computing and Information Science. The system described in this report was designed, implemented and tested as part of the project, and all sources of information used have been duly acknowledged in the references. The test results reported in Chapter Four were obtained by running the implemented system; where an evaluation activity (such as the user questionnaire) had not yet been carried out, this is stated clearly rather than replaced with assumed figures.")
    story.append(Spacer(1, 0.6 * inch))
    sig2 = g["Table"]([["_______________________________", "_______________________________"], [STUDENT_NAME, "Date"]], colWidths=[3.3 * inch, 2.6 * inch])
    sig2.setStyle(g["TableStyle"]([("FONTNAME", (0, 0), (-1, -1), "Times-Roman"), ("FONTSIZE", (0, 0), (-1, -1), 11.5)]))
    story.append(sig2)

    front_heading("ACKNOWLEDGEMENT")
    P("I acknowledge the support of the Department of Computing and Information Science, lecturers, project supervisors, sports organisers, coaches, students and other stakeholders whose perspectives are relevant to the design of a practical sports event management platform. I also acknowledge the role of modern web technologies in making information systems more accessible for organisational activities.")

    front_heading("ABSTRACT")
    P("Sports events contribute to physical fitness, teamwork, discipline, talent discovery and social interaction, yet their administration becomes difficult when registration, scheduling, score recording and result publication depend on paper records, spreadsheets and verbal communication. This project designed and implemented a Web-Based Sports Event Management System for educational institutions and small organisations, and deployed it as <i>BOUESTI Sports</i>, a sports portal for the internal student competitions of Bamidele Olumilua University of Education, Science and Technology, Ikere-Ekiti. The system is a centralised platform through which administrators manage seasons, competitions, teams, athletes, fixtures, scores, match events, line-ups, news and media, while participants and spectators view schedules, live and final scores, automatically computed standings, player statistics and published results. Students can also create accounts, apply for team trials and send enquiries. The development followed a system development research design with iterative and incremental implementation. The system uses a three-tier client-server architecture implemented with the Laravel 12 PHP framework (Model-View-Controller pattern), a MySQL/MariaDB relational database, and a responsive interface built with HTML, CSS, JavaScript, Blade templates, Tailwind CSS and Alpine.js, developed with Visual Studio Code and XAMPP. The system was verified with 96 automated feature and unit tests (921 assertions), all of which passed. The tests cover registration, authentication, role-based access, team and player management, fixture scheduling with conflict detection, score recording and result publication. Responsiveness was checked on all 27 public pages at ten screen widths from 320 to 1440 pixels, and common pages were served in a median of 43–82 milliseconds in the local test environment. The results show that a centralised web system removes manual computation of standings, prevents common data-entry errors, preserves historical records and makes sports information accessible on any device. A user-satisfaction questionnaire has been prepared for post-deployment evaluation.")
    P("<b>Keywords:</b> sports event management, web-based information system, fixture scheduling, score tracking, Laravel, MVC, relational database, three-tier architecture.", "bodyleft")

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
        ["ERD", "Entity-Relationship Diagram"], ["FR / NFR", "Functional / Non-Functional Requirement"],
        ["HTML", "HyperText Markup Language"], ["HTTP", "HyperText Transfer Protocol"],
        ["MVC", "Model-View-Controller"], ["ORM", "Object-Relational Mapping"],
        ["OWASP", "Open Worldwide Application Security Project"], ["PHP", "PHP: Hypertext Preprocessor"],
        ["RDBMS", "Relational Database Management System"], ["SQL", "Structured Query Language"],
        ["TC", "Test Case"], ["UI", "User Interface"], ["URL", "Uniform Resource Locator"],
        ["XAMPP", "Cross-platform Apache, MariaDB/MySQL, PHP and Perl package"], ["XSS", "Cross-Site Scripting"],
    ], [1.5 * inch, 4.5 * inch])

    story.extend(body)


# =========================================================================== CHAPTER ONE
def chapter_one(g):
    P, bullets, numbered, h2, chapter, table, figure = g["P"], g["bullets"], g["numbered"], g["h2"], g["chapter"], g["table"], g["figure"]
    inch = g["inch"]

    chapter("ONE", "INTRODUCTION")
    h2("1.1 Background of the Study")
    P("Sports activities are an important component of educational and organisational development because they provide opportunities for physical exercise, teamwork, discipline, leadership, healthy competition and social interaction. Within schools and universities, activities such as football tournaments, athletics competitions, basketball games and inter-house sports are frequently used to encourage participation and identify talented athletes. Sports participation can therefore contribute to the development of individuals and communities.")
    P("Although the sporting activity itself is physical, its administration is fundamentally an information-management task. Before a match takes place, organisers must know who is registered, which team a participant belongs to, what competition is being played, where and when the event will occur, and which officials or organisers are responsible. During the competition, scores and other results must be captured correctly. After the competition, standings and results must be communicated and preserved for reference.")
    P("In many small institutions and organisations, these administrative activities are still handled with paper records, spreadsheets, verbal announcements or combinations of these approaches. Such methods can be adequate when an event is very small, but their weaknesses become more visible as the number of teams, participants and fixtures increases. Records may be misplaced, duplicated or entered incorrectly; fixtures can overlap; changes may not reach all participants promptly; and spectators may have difficulty obtaining current results.")
    P("Scheduling conflicts, loss of important records, delays in updating match results, difficulties in coordinating participants and human error are recurring problems of manual sports event management. These issues are not merely clerical inconveniences. Incorrect registration information can affect eligibility; a fixture conflict can disrupt an entire tournament schedule; and inaccurate scores can undermine trust in competition results.")
    P("Web-based information systems offer a way to centralise these activities. A user can access a web application through an internet-enabled browser, while the application processes requests and stores information in a database. In the sports event management context, this arrangement makes it possible to create a single source of event information that can be updated by authorised users and accessed by relevant stakeholders.")
    P("The project therefore focuses on the design and implementation of a web-based sports event management system capable of supporting athlete registration, team management, event scheduling, score tracking and result reporting. The system follows a client-server model and a three-tier architecture consisting of the presentation, application and database layers (Section 3.9). It is built with HTML, CSS, JavaScript, PHP and MySQL — with the Laravel framework organising the PHP code — using Visual Studio Code and XAMPP as development tools. To evaluate the design on a real case, the system was implemented as <i>BOUESTI Sports</i>, the sports portal of Bamidele Olumilua University of Education, Science and Technology (BOUESTI), Ikere-Ekiti, where seven student football clubs (Amapro FC, Elite FC, Young Boys FC, CSC Elites, Engines Boys, Sovereignty FC and AMCOMS) compete internally and basketball is also played on the university's courts.")
    P("An important feature of the system is centralisation. Rather than requiring an organiser to maintain separate paper registers, spreadsheets and announcement channels, the platform maintains related records within one database. Registration information is connected to teams, teams to competitions, fixtures to teams, and scores and match events to fixtures. Standings and statistics are then computed from those records. This relational structure supports consistency and makes information easier to retrieve.")
    P("The system is also intended to improve communication. Participants can see match schedules and competition results without depending on verbal communication, and spectators obtain published information through the same platform. Administrators update information from a dashboard, reducing the delay between an event and the publication of its official record.")
    P("The study is situated within computer science because it applies principles of software engineering, database management, web development, information systems analysis, user interface design and system testing to a practical organisational problem. It is not intended to replace sports officials or the human decisions required in event management. Rather, it provides information-processing support for the repetitive administrative tasks that surround sporting activities.")

    h2("1.2 Statement of the Problem")
    P("Manual management of sports events creates difficulties in maintaining accurate and accessible records. Paper documents can be misplaced or damaged, while spreadsheet files may exist in multiple versions. When several people update records without a common system of control, inconsistencies occur.")
    P("Scheduling is another important problem. A sports competition may involve many teams, venues and time slots. If fixture preparation is performed manually, an organiser may accidentally assign the same team to two matches at the same time or fail to communicate a change in schedule. The result can be confusion, delayed matches and unnecessary administrative workload.")
    P("Score and result management also require accuracy. Scores must be recorded against the correct fixture and be available to authorised organisers for verification before results are published. Where information is kept in disconnected records, updating one copy does not necessarily update another. This can produce discrepancies between the score sheet, the standings and the public announcement; league tables computed by hand are particularly prone to arithmetic errors.")
    P("Communication is affected when there is no centralised digital platform. Participants and spectators may have to ask organisers directly for fixture information or results. This increases the communication burden on organisers and causes information to spread at different times to different people.")
    P("Finally, manual approaches make it difficult to produce a consistent historical record of an event. The problem addressed by this project is therefore the absence or inadequacy of an integrated, accessible and reliable web-based mechanism for managing the core administrative activities of small-scale sports events.")

    h2("1.3 Motivation for the Study")
    P("The motivation for the study arises from the gap between the increasing amount of information generated by sports competitions and the limitations of manual administrative processes. Smaller institutions and local organisations often continue to rely on manual methods because accessible and affordable digital solutions are not always available to them.")
    P("The project responds by concentrating on essential functions rather than attempting to build an unnecessarily complex professional sports platform. The system provides practical support for registration, team management, scheduling, score recording and result publication. This focus makes the project appropriate for educational institutions and small organisations.")
    P("Another motivation is the educational value of the project. Building the system provides an opportunity to apply computer science concepts to a complete information-system lifecycle: problem identification, requirements analysis, interface design, database modelling, programming, testing and evaluation. The project therefore has both practical and academic value.")

    h2("1.4 Aim and Objectives")
    P("The <b>aim</b> of this project is to design and implement a web-based sports event management system that improves the organisation, scheduling and tracking of sports competitions. The specific <b>objectives</b> are:")
    numbered([
        "To design a user-friendly web-based system for efficient management of sports events, including registration, scheduling and result tracking.",
        "To implement a functional platform that enables organisers to manage competitions, teams and scores while allowing users to access event information easily.",
        "To evaluate the system's performance, usability and effectiveness in improving the organisation, coordination and transparency of sports event management.",
    ])

    h2("1.5 Research Questions")
    numbered([
        "How can a web-based system improve the registration and management of sports participants and teams?",
        "How can centralised digital scheduling reduce the likelihood of fixture conflicts and communication delays?",
        "How can a database-supported score and result module improve the accuracy and accessibility of sports competition information?",
        "How usable and effective is the proposed system for organisers, participants and spectators?",
        "To what extent can the proposed system reduce the administrative burden associated with manual sports event management?",
    ])

    h2("1.6 Significance of the Study")
    table("1.1: Significance of the study to stakeholders", ["Stakeholder", "Expected significance"], [
        ["Sports organisers", "Centralised registration, scheduling, score and result management; standings computed automatically; reduced repetitive administrative work."],
        ["Athletes and teams", "Easier registration and trial applications; access to fixtures, results and individual statistics."],
        ["Spectators", "Improved access to published schedules, live and final scores, standings and results on any device."],
        ["Educational institutions", "Better coordination and documentation of sports activities; a professional digital presence for campus sport."],
        ["Researchers and developers", "A reference model for applying web technologies to sports event management."],
    ], [2.0 * inch, 4.0 * inch])
    P("The significance of the study is primarily practical. Organisers require reliable information at different stages of an event. A centralised platform makes the same information available through controlled interfaces, thereby improving coordination.")
    P("The study is also significant from a documentation perspective. Digital records can be searched and retained more easily than scattered paper documents. This supports institutional reporting and future reference, provided appropriate data protection and backup procedures are used.")
    P("Finally, the study contributes to computer science education by demonstrating how a real-world administrative problem can be translated into requirements, data models, software modules and testable system functions.")

    h2("1.7 Scope of the Study")
    P("The study covers the design and implementation of a web-based sports event management system for an educational institution or small organisation, implemented for the internal competitions of BOUESTI. The core functions are user registration and authentication, athlete and team management, competition (event) creation, fixture scheduling with conflict detection, score and match-event recording, automatic computation of standings and statistics, result publication and administrative management. Supporting functions include online trial applications, contact messages, news, a photo gallery and videos.")
    P("The scope includes a responsive web interface for the public, a role-protected administrator dashboard, server-side processing, a relational database and automated system testing focused on functionality, validation, security, data integrity, responsiveness and response time. Football is managed in full; basketball is presented as an information section (programme, courts and how to get involved), with basketball fixtures and results recommended for a future version.")
    P("The project does not cover live video streaming, online payment for participation, or integration with external sports federation databases.")

    h2("1.8 Limitations of the Study")
    bullets([
        "Academic time constraints restricted the inclusion of advanced features such as real-time push notifications and automatic fixture generation.",
        "Development and testing were conducted in a local environment (XAMPP) rather than on large-scale production hosting infrastructure, so large-scale operational performance cannot be claimed without field deployment.",
        "Verified squad lists were not available in machine-readable form, so the system was tested with a generated sample season of fictional players; a CSV import tool is provided for real data.",
        "Users require suitable internet connectivity and a web-enabled device.",
        "The user-satisfaction questionnaire (Appendix C) is designed for administration after deployment; its results are therefore not part of this report.",
        "The system is intentionally focused on core sports-event administration rather than every possible feature of a professional sports management platform.",
    ])

    h2("1.9 Operational Definition of Terms")
    table("1.2: Operational definition of terms", ["Term", "Operational meaning in this study"], [
        ["Web-based system", "An application accessed through a web browser over a network."],
        ["Sports event", "An organised sporting competition (league, cup, friendly or tournament) managed through the platform."],
        ["Administrator", "An authorised user responsible for configuring competitions, teams, fixtures, scores and results."],
        ["Participant", "An athlete or team member whose information is managed by the system."],
        ["Fixture", "A scheduled pairing of a home team and an away team for a particular match."],
        ["Score", "The recorded number of goals (or points) of each team in a match."],
        ["Match event", "A recorded incident in a match, such as a goal, assist, card or substitution."],
        ["Result", "The published outcome of a completed match, shown publicly once the match status is full time."],
        ["Standings", "The ranking of teams in a competition, computed from completed results."],
        ["Database", "A structured collection of system information stored and retrieved electronically."],
        ["Three-tier architecture", "A design separating presentation, application processing and data storage responsibilities."],
    ], [1.8 * inch, 4.2 * inch])

    h2("1.10 Organisation of the Report")
    P("The report is organised into five chapters. Chapter One introduces the study and presents the background, problem, motivation, aim, objectives, research questions, significance, scope and limitations. Chapter Two reviews concepts and related work relevant to sports event management, web-based systems, scheduling, databases and software architecture. Chapter Three presents the methodology, requirements analysis and system design. Chapter Four describes the implementation and presents the results of testing and evaluation. Chapter Five summarises the work, draws conclusions, presents recommendations and identifies future extensions. The appendices contain the data dictionary, the completed test sheet, the user questionnaire, module specifications, acceptance criteria, a user and installation guide and selected source code.")


# =========================================================================== CHAPTER TWO
def chapter_two(g):
    P, bullets, h2, h3, chapter, table = g["P"], g["bullets"], g["h2"], g["h3"], g["chapter"], g["table"]
    inch = g["inch"]

    chapter("TWO", "LITERATURE REVIEW")
    h2("2.1 Introduction")
    P("This chapter reviews concepts relevant to the design of a web-based sports event management system. Information technology, web-based management systems, software automation and sports administration are the central areas of interest. The review moves from the general concept of sports event management to the information-system functions required to support it, before considering architecture, frameworks, database design, security, usability and the gap that motivates the project.")

    h2("2.2 Concept of Sports Event Management")
    P("Sports event management involves planning, coordinating, administering and documenting the activities required for a sporting competition. Although the exact responsibilities vary according to the sport and size of the event, common activities include participant registration, team formation, scheduling, venue coordination, officiating, score recording, result publication and communication.")
    P("From an information-system perspective, sports event management can be viewed as a set of related entities and processes. Participants belong to teams; teams take part in competitions; competitions contain matches; matches have schedules and scores; and completed matches generate results and standings. The relationships between these objects make a structured database useful.")
    P("An effective management platform should therefore support both data capture and information presentation. It is not sufficient to store registration data if organisers cannot retrieve it quickly. Similarly, recording scores has limited value if participants cannot access verified results. The system must connect administrative functions with user-facing information.")

    h2("2.3 Sports Event Administration")
    P("Sports administration begins before competition day. Organisers identify the sport, define the competition structure, collect entries and verify participants. Registration data forms the foundation for later processes because fixtures and results depend on knowing which teams or individuals are eligible.")
    P("During competition, administration becomes time-sensitive. Organisers need to know which match is next, where it will take place, which teams are involved and whether previous results have been recorded. After matches, scores must be entered and verified, and the appropriate result made available to users.")
    P("These activities demonstrate why a centralised system is valuable. Each process produces information that is consumed by another process: a team registered in the system later appears in a fixture; that fixture later receives a score; and the score becomes part of the published results and standings.")

    h2("2.4 Information Systems in Event Management")
    P("Information systems combine people, processes, data and technology to support organisational activities (Laudon and Laudon, 2020). Web-based management systems have been adopted in many sectors to automate administrative processes and improve data accessibility.")
    P("Automation is particularly useful for repetitive operations. A registration form can validate required fields before data are stored. A scheduling module can retrieve available teams and competition information from the database and check for conflicts. A result module can associate a score with an existing fixture rather than requiring an organiser to rewrite all fixture details. A well-designed system captures each fact once, at its source, and derives other information — such as standings — automatically.")
    P("Pressman and Maxim (2020) describe software as a means of automating complex tasks, reducing human error and improving organisational efficiency, while Sommerville (2016) emphasises the need for maintainable and scalable applications that are understood in relation to the organisation that uses them.")

    h2("2.5 Web-Based Information Systems")
    P("A web-based information system provides functionality through a browser-based interface over the HyperText Transfer Protocol, whose architectural principles were formalised by Fielding (2000). The major advantage in the context of this study is accessibility: authorised users can interact with the application without installing software on every device.")
    P("A typical web system separates presentation from processing and data storage. The browser presents forms and pages, the server handles business rules and authentication, and the database stores persistent information. This separation supports maintainability because a change to one layer does not necessarily require redesigning every other layer.")
    P("Web systems also support centralised updates. When an administrator publishes a new result, the updated information is retrieved from the same database by every user accessing the relevant page. This is preferable to maintaining multiple independent copies of a result list.")

    h2("2.6 Sports Registration and Team Management")
    P("Registration is the process through which participant and team information enters the system. A useful registration module captures enough information to identify the participant or team while avoiding unnecessary duplication. Validation is important because incorrect email addresses, empty names or duplicated identifiers make later processes unreliable.")
    P("Team management extends registration by associating athletes with teams and teams with competitions. The database should represent these relationships explicitly, allowing the application to answer questions such as which players belong to a team or which teams are taking part in a competition.")
    P("Role-based access is also relevant. An administrator may create or modify team information, while an ordinary participant may only view information relevant to the event. Separating permissions reduces the risk of unauthorised modification.")

    h2("2.7 Scheduling and Fixture Management")
    P("Scheduling is one of the most coordination-intensive functions in a sports event. A fixture must identify the participating teams, date, time, venue and competition stage. The schedule must also avoid obvious conflicts within the constraints of the event.")
    P("Automated scheduling does not eliminate the need for human judgement. Instead, the system stores and displays the schedule consistently and applies basic validation rules — for example, preventing a team from being assigned to two fixtures on the same day, or a team from being drawn against itself.")
    P("Fixture management should also support updates. If a match is postponed or a time changes, the administrator should be able to update the central record so that subsequent users retrieve the new information rather than an outdated copy. Table 2.1 illustrates the kind of schedule a fixture module maintains.")
    table("2.1: Illustrative fixture and scheduling view", ["Match", "Home team", "Away team", "Date", "Time", "Status"], [
        ["M01", "Team Alpha", "Team Beta", "Day 1", "10:00", "Scheduled"],
        ["M02", "Team Gamma", "Team Delta", "Day 1", "12:00", "Scheduled"],
        ["M03", "Winner M01", "Winner M02", "Day 2", "14:00", "Pending"],
    ], [0.7 * inch, 1.2 * inch, 1.2 * inch, 0.8 * inch, 0.8 * inch, 1.3 * inch])

    h2("2.8 Score and Result Management")
    P("Score management requires a clear link between a score and the match to which it belongs. A result should not exist independently of a fixture because that creates ambiguity. The database design should therefore identify matches with unique identifiers and associate scores with those identifiers.")
    P("Result publication is the point at which internal administrative data becomes information for participants and spectators. The system should distinguish between an editable administrative record and a published result. This separation reduces accidental public display of incomplete information.")
    P("Most association football leagues rank teams by awarding three points for a win, one for a draw and none for a defeat, breaking ties by goal difference and then goals scored. Because standings are a pure function of the completed results, they should be computed from the results rather than stored and edited by hand; this removes a whole class of inconsistency errors while preserving the integrity of previously recorded results.")

    h2("2.9 Database Management")
    P("The relational model, introduced by Codd (1970), represents data as tables related through primary and foreign keys. It is suitable for this project because the core data have clear relationships: users, teams, players, competitions, matches and match events. Normalisation reduces repetition and update anomalies (Elmasri and Navathe, 2016; Connolly and Begg, 2015).")
    P("Database integrity is important in sports management. A match should refer to valid teams, and a match event should refer to a valid match and a player of one of its teams. Constraints and application-level validation prevent orphan records, and unique identifiers reduce accidental duplication.")
    P("MySQL (and its compatible fork MariaDB, bundled with XAMPP) is appropriate for a conventional web application because it supports relational tables, structured queries, indexes, constraints and transactions needed for common CRUD operations.")

    h2("2.10 Three-Tier Architecture and the MVC Pattern")
    P("A three-tier architecture separates the presentation layer, the application layer and the database layer. The presentation layer is the user-facing component built with HTML, CSS and JavaScript. The application layer handles requests, authentication, scheduling logic, score updates and communication with the database. The database layer stores persistent information.")
    P("Separating these concerns has several advantages. The user interface can evolve without changing the underlying data model; business rules are centralised on the server; and database access is controlled rather than exposed directly to the browser.")
    P("Within the application layer, the Model-View-Controller (MVC) pattern, first described for Smalltalk-80 user interfaces (Krasner and Pope, 1988), separates the <b>Model</b> (data and business rules), the <b>View</b> (presentation) and the <b>Controller</b> (handling input and coordinating the model and view). This separation improves maintainability and testability (Gamma et al., 1994; Fowler, 2002).")

    h2("2.11 Web Development Frameworks")
    P("A web framework provides the common infrastructure required by most web applications — routing, request handling, database access, templating, authentication, validation and security protections — so that developers can concentrate on application-specific logic. Laravel is an open-source PHP framework that follows the MVC pattern and provides the Eloquent object-relational mapper, database migrations, the Blade templating engine, form-request validation, middleware, authentication scaffolding and built-in support for automated testing (Laravel, 2025). Using a mature framework reduces the amount of security-sensitive code that must be written by hand.")

    h2("2.12 Security, Privacy and Access Control")
    P("Sports systems contain personal information such as participant names and contact details. Security should therefore be considered even when the system is designed for a small institution. The OWASP Top 10 (OWASP Foundation, 2021) lists the most critical web application risks, including broken access control, injection and cross-site scripting.")
    P("Authentication ensures that only registered users access protected functions, while authorisation distinguishes administrative functions from ordinary viewing functions. Passwords should never be stored as plain text; they should be hashed. Server-side validation should be applied even when client-side validation exists. Database queries should use parameter binding to prevent injection, output should be escaped, forms should carry anti-forgery tokens, and sessions should be managed carefully. Backup procedures are also important because a centralised database becomes a critical source of institutional information.")

    h2("2.13 Usability in Sports Event Systems")
    P("Usability refers to how easily intended users can understand and operate the system (Nielsen, 1993). Sports organisers may need to perform tasks quickly while an event is active, so interfaces should use clear labels, predictable navigation, concise forms and clear error messages.")
    P("Participants and spectators have different needs. Administrators require management functions, while spectators mainly need schedules, scores and results. A well-designed system should not expose administrative controls to ordinary users.")
    P("Responsive web design (Marcotte, 2010) is also relevant because users access schedules and results from phones, tablets and desktop computers. Accessibility guidelines such as WCAG 2.1 (W3C, 2018) add criteria such as keyboard operability, visible focus and text alternatives for images.")

    h2("2.14 Review of Related Approaches")
    P("Digital systems for athlete registration, match scheduling, score tracking and result publication are widely used, from official websites of professional competitions to subscription-based platforms for amateur leagues. For a small institution, however, general solutions may involve subscription costs, may not be branded to the institution, may not model student-specific information, and may store participant data with third parties. Table 2.2 compares the main approaches functionally.")
    table("2.2: Comparison of manual and web-based approaches", ["Function", "Manual approach", "Web-based approach (this project)"], [
        ["Registration", "Paper forms / spreadsheets", "Centralised electronic registration and trial applications"],
        ["Team management", "Separate lists", "Linked team and player records"],
        ["Scheduling", "Manually prepared fixtures", "Central fixture records with conflict validation"],
        ["Score tracking", "Paper score sheets / announcements", "Database-linked scores and match events"],
        ["Standings", "Computed by hand", "Computed automatically from completed results"],
        ["Result publication", "Notice boards / verbal updates", "Web-accessible published results"],
        ["Documentation", "Scattered records", "Searchable structured database preserved across seasons"],
    ], [1.5 * inch, 2.0 * inch, 2.5 * inch])

    h2("2.15 Theoretical Perspective")
    P("The study can be understood through the systems-development perspective, in which a problem domain is analysed, requirements are identified, and a solution is designed, implemented and evaluated. Software engineering principles provide a further foundation: requirements should be traceable to implemented functions, components should have clear responsibilities, and testing should provide evidence that functions behave as intended (Sommerville, 2016; Beck, 2002).")
    P("From an information-management perspective, the project emphasises the transformation of raw sports-event data into useful information. Registration entries, fixtures, scores and match events are raw records; schedules, standings, player statistics and published results are organised outputs that support decision-making and communication.")

    h2("2.16 Gap in Literature and Rationale for the Study")
    P("The central gap is practical rather than a claim that no sports-management systems exist. Smaller institutions and local organisations still depend on manual processes and lack accessible, affordable digital solutions that fit their structure.")
    P("The project therefore targets simplicity and integration. Instead of focusing on advanced professional features, it concentrates on a coherent set of core functions that can be deployed in an educational or small-organisation context. The value of the work lies in bringing registration, scheduling, score management, standings and result publication into one institution-owned platform.")
