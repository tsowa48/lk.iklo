create table users(id bigserial primary key not null, fio text not null, login text not null, password text not null, facial char(20), kid bigint not null);
create table pu(uid bigint not null, pid bigint not null);
create table post(id bigserial primary key not null, name text not null, type int not null);
create table komission(id bigserial primary key not null, parent bigint, name text not null, number bigint not null, voters bigint, hkoib int, hkeg int);
create table vote(id bigserial primary key not null, name text not null, day int not null, year int not null);
create table kv(kid bigint not null, vid bigint not null, first int not null, last int not null);
create table rate(vid bigint not null, uid bigint not null, coef decimal);
create table bet(vid bigint not null, kid bigint not null, pid bigint not null, price decimal not null);
create table schedule(vid bigint not null, uid bigint not null, day int not null, start int not null, finish int);

create table klass(tid bigint not null, kid bigint, pid bigint, uid bigint);

create table test(id bigserial primary key not null, vid bigint not null, text text not null, passball decimal not null);
create table question(id bigserial primary key not null, tid bigint not null, text text not null, type int not null);
create table answer(id bigserial primary key not null, qid bigint not null, text text not null, ball decimal not null);
create table result(uid bigint not null, tid bigint not null, ball decimal not null);
create table incorrect(tid bigint not null, qid bigint not null, uid bigint not null);

create table calendar(doy int not null primary key, day text not null, isholy int not null);

insert into post(name,type) values('Председатель',0);
insert into post(name,type) values('Заместитель председателя',0);
insert into post(name,type) values('Секретарь',0);
insert into post(name,type) values('Член комиссии',0);
--insert into post(name,type) values('Резерв',0);
insert into post(name,type) values('Бухгалтер',1);
insert into post(name,type) values('Оператор КОИБ',1);
insert into post(name,type) values('Администратор',1);

insert into komission(name,number) values('ЦИК РФ',0);
insert into komission(parent,name,number) values(1,'Избирательная комиссия Липецкой области',48);
