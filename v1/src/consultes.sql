#update de totes les ordres per a que siguin per avui

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

select teams.code, 
teams.name,  
concat(workers.name, ' ', workers.surname) as worker

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

select processes.code, 
processes.description, 
points.pos_x, 
points.pos_y, 
points.pos_z, 
case 
	when points.tweezer = 0 then 'tancada' 
    when points.tweezer = 1 then 'oberta' end as pinca 
from processes
inner join points on points.id_process = processes.id

#5

select orders.code as order_code,  
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
	and (robots.name like 'First Robot' or robots.code = 100) 
join status_order on status_order.id = orders.id_status_order 
	and status_order.description = 'pending'
left join tasks on tasks.id_order = orders.id
left join workers on workers.id = tasks.id_worker
left join teams on teams.id = tasks.id_team

where teams.name = 'EquipA' 
	and (workers.name = 'Andreu' or workers.surname = 'Andreu' or 'Andreu' = '')

#8.2

