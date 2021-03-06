﻿#update de totes les ordres per a que siguin per avui

update orders set date = cast(concat(curdate(), " 23:59:59") as datetime);


#update de les ordres amb tasca per a que totes siguin a dia d'avui, si és vol és pot escollir per treballador.

#drop table if exists a00;
#create temporary table a00 as
#select orders.id from workers
#join tasks on workers.id = tasks.id_worker
#join orders on orders.id = tasks.id_order;
#where workers.id = 4;

#update orders set date = now() where orders.id in (select * from a00);


#update de la data de les tasques per a que el dia d'assignació sigui avui aixi com les ordres 
#(les ordres que falitn per assignar estaran sense tasca).

drop table if exists a00;
create temporary table a00 as
select tasks.id, orders.date from tasks
join orders on orders.id = tasks.id_order;

update tasks set date_assignation = now() where tasks.id in (select id from a00);


#update que assigna les ordres completades data i hora d'avui

drop table if exists a00;
create temporary table a00 as
select tasks.id from orders
join status_order on status_order.id = orders.id_status_order and status_order.id = 3
join tasks on tasks.id_order = orders.id;

update tasks set date_completion = now() where tasks.id in (select * from a00);


#1

select workers.code,
workers.username, 
workers.nif, 
workers.name, 
workers.surname, 
workers.mobile, 
workers.telephone, 
workers.category, 
teams.name as team_name

from workers
inner join teams on teams.id = workers.id_team;



#2

select teams.id,
teams.code, 
teams.name,  
(select group_concat(workers.name, ' ', workers.surname) as worker from workers where workers.id_team = teams.id)
from teams
inner join workers on workers.id_team = teams.id

#3
select robots.code, 
robots.name, 
concat(robots.latitude,'/',robots.longitude) as ubication, 
status_robot.description as robot_status

from robots 
inner join status_robot on status_robot.id = robots.id_current_status

#4

select distinct processes.code, 
processes.description, 

(select group_concat("X:",points.pos_x, 
" Y:",points.pos_y, 
" Z:",points.pos_z, 
" Pinza:",if(points.tweezer=0, 'cerrada', 'abierta')) as move from points where points.id_process = processes.id) as move

from processes
inner join points on points.id_process = processes.id

#5

select orders.id as order_id,
orders.code as order_code,  
orders.description as order_description,
orders.priority as order_priority,
orders.date as order_date,
processes.description as process_description,
orders.quantity as order_quantity,
robots.name as robot_name,
robots.code as robot_code,
status_order.description as status_order_description

from orders
inner join processes on processes.id = orders.id_process
inner join robots on robots.id = orders.id_robot
inner join status_order on status_order.id = orders.id_status_order

#8.1
select concat(workers.name, workers.surname) as workers_user,
teams.name as team_name,
orders.code as order_code,
orders.description as order_description,
orders.priority as order_priority,
orders.date as order_date,
processes.description as process_description,
orders.quantity as order_quantity,
robots.name as robot_name,
robots.code as robot_code,
status_order.description as status_order_description

from orders
join processes on processes.id = orders.id_process
join robots on robots.id = orders.id_robot 
join status_order on status_order.id = orders.id_status_order 
	and status_order.description = 'pending'
left join tasks on tasks.id_order = orders.id
left join workers on workers.id = tasks.id_worker
left join teams on teams.id = tasks.id_team

where (teams.id = 3 or 3 = 0)
	and (workers.id = 0 or 0 = 0)
	and orders.date between date_format(curdate(),'%Y-01-01') and cast(concat(curdate(), ' 23:59:59') as datetime);

#8.2

#tasks consult

select concat(workers.name,' ',workers.surname) as worker,
count(tasks.id_worker) as tasks_done
from tasks
join orders on orders.id = tasks.id_order
left join workers on workers.id = tasks.id_worker
where tasks.id_worker is not null and orders.id_status_order = 3
and (tasks.date_completion between '01/01/2016' and '31/12/2016' or tasks.date_completion is not null)
group by tasks.id_worker

select teams.name as team,
count(tasks.id_team) as tasks_done
from tasks
join orders on orders.id = tasks.id_order
left join teams on teams.id = tasks.id_team
where tasks.id_team is not null and orders.id_status_order = 3
and (tasks.date_completion between '01/01/2016' and '31/12/2016' or tasks.date_completion is not null)
group by tasks.id_team

# Consulta amb taules temporals estadistiques teams

drop table if exists a00;
create temporary table a00 as
select teams.name, teams.id from teams;

drop table if exists a01;
create temporary table a01 as
select count(*) as tasks_done, tasks.id_team from orders
left join tasks on orders.id = tasks.id_order
where orders.id_status_order = 3
and (tasks.date_completion between '2016-05-23 12:59:59' and '2016-05-23 12:59:59' and tasks.date_completion is not null)
group by tasks.id_team;


select a00.name, if(a01.tasks_done is null, 0, a01.tasks_done) as tasks_done
from a00
left join a01 on a00.id = a01.id_team;

# Consulta amb taules temporals estadistiques workers

drop table if exists a00;
create temporary table a00 as
select concat(workers.name, ' ', workers.surname) as worker, workers.id from workers;

drop table if exists a01;
create temporary table a01 as
select count(*) as tasks_done, tasks.id_worker from orders
left join tasks on orders.id = tasks.id_order
where orders.id_status_order = 3
and (tasks.date_completion between '2016-01-23 12:59:59' and '2016-06-23 12:59:59' and tasks.date_completion is not null)
group by tasks.id_worker;


select a00.worker, if(a01.tasks_done is null, 0, a01.tasks_done) as tasks_done
from a00
left join a01 on a00.id = a01.id_worker;

# dashboard select robots and status

select st.description, count(*) as robots_qnt
from robots as r
inner join status_robot as st on r.id_current_status = st.id
group by st.id