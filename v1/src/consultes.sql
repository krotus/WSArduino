#update de totes les ordres per a que siguin per avui

update orders set date = now();


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
