CREATE DATABASE IF NOT EXISTS api_rest_resume;
USE api_rest_resume;

CREATE TABLE users(
    id                  int(255) auto_increment NOT NULL,
    name                varchar(50) NOT NULL,
    surname             varchar(50),
    email               varchar(255) NOT NULL,
    password            varchar(255) NOT NULL,
    description         text,
    image               varchar(255),
    created_at          datetime DEFAULT NULL,
    updated_at          datetime DEFAULT NULL,
    remember_token      varchar(255),
    CONSTRAINT pk_users PRIMARY KEY(id)
) ENGINE=InnoDB;

CREATE TABLE resumes(
    id              int(255) auto_increment NOT NULL,
    user_id         int(255) NOT NULL,    
    name            varchar(50) NOT NULL,
    created_at      datetime DEFAULT NULL,
    updated_at      datetime DEFAULT NULL,
    CONSTRAINT pk_resumes PRIMARY KEY(id)
)ENGINE=InnoDB;

CREATE TABLE projects(
    id              int(255) auto_increment NOT NULL,
    user_id         int(255) NOT NULL, 
    resume_id       int(255) DEFAULT NULL, 
    name            varchar(50) NOT NULL,
    description     text,
    company         varchar(100),
    started_date    datetime DEFAULT NULL,
    end_date        datetime DEFAULT NULL,
    created_at      datetime DEFAULT NULL,
    updated_at      datetime DEFAULT NULL,
    CONSTRAINT pk_projects PRIMARY KEY(id),
    CONSTRAINT fk_projects_user FOREIGN KEY (user_id) REFERENCES users(id)
)ENGINE=InnoDB;

CREATE TABLE projects_resume_relation(
    project_id         int(255) NOT NULL,
    resume_id          int(255) NOT NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (resume_id) REFERENCES resumes(id),
    UNIQUE (project_id, resume_id)
)ENGINE=InnoDB;

CREATE TABLE tools(
    id              int(255) auto_increment NOT NULL,
    user_id         int(255) NOT NULL, 
    name            varchar(50) NOT NULL,
    created_at      datetime DEFAULT NULL,
    updated_at      datetime DEFAULT NULL,
    CONSTRAINT pk_tools PRIMARY KEY(id),
    CONSTRAINT fk_tools_user FOREIGN KEY (user_id) REFERENCES users(id)
)ENGINE=InnoDB;

CREATE TABLE tools_projects_relation(
    tool_id            int(255) NOT NULL,
    project_id         int(255) NOT NULL,
    FOREIGN KEY (tool_id) REFERENCES tools(id),
    FOREIGN KEY (project_id) REFERENCES projects(id),
    UNIQUE (tool_id, project_id)
)ENGINE=InnoDB;

INSERT INTO users(name,surname,email,password,created_at,picture) VALUES('Emmanuel',' Cobian Zamora','emmanuel@gmail.com','12345','2020-07-19','');
INSERT INTO users(name,surname,email,password,created_at,picture) VALUES('Memo',' Cobian Zamora','memorama@gmail.com','9898','2020-07-19','');

INSERT INTO resumes(user_id,name) VALUES(2,'Test Reume1');
INSERT INTO resumes(user_id,name) VALUES(1, 'Test Resume 2');

INSERT INTO projects(name,description,started_date,end_date,user_id) VALUES("Task Manager","Web page to control the projects progress adding tasks and mark them as finished and adding new projects.",'2019-08-19','2019-07-12',1);
INSERT INTO projects(name,description,started_date,end_date,user_id) VALUES("QR Poliza","Generate a QR code with the data of a poliza to scan it and show all the data in a we page",'2019-06-10','2019-08-05',1);

INSERT INTO tools(name,user_id) VALUES("PHP",1);
INSERT INTO tools(name,user_id) VALUES("HTML5",1);
INSERT INTO tools(name,user_id) VALUES("JavaScript",1);
INSERT INTO tools(name,user_id) VALUES("Java",1);
INSERT INTO tools(name,user_id) VALUES("Eclipse",1);
INSERT INTO tools(name,user_id) VALUES("NodeJS",1);

INSERT INTO tools_projects_relation(project_id,tool_id) VALUES(1,1);
INSERT INTO tools_projects_relation(project_id,tool_id) VALUES(1,2);
INSERT INTO tools_projects_relation(project_id,tool_id) VALUES(1,3);
INSERT INTO tools_projects_relation(project_id,tool_id) VALUES(2,4);
INSERT INTO tools_projects_relation(project_id,tool_id) VALUES(2,5);
INSERT INTO tools_projects_relation(project_id,tool_id) VALUES(2,1);
INSERT INTO projects_resume_relation(resume_id,project_id) VALUES(1,1);
INSERT INTO projects_resume_relation(resume_id,project_id) VALUES(1,2);
INSERT INTO projects_resume_relation(resume_id,project_id) VALUES(2,1);