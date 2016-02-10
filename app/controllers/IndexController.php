<?php
namespace Gabs\Controllers;
use Gabs\Models\Personas;
 
class IndexController extends ControllerBase
{
    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {
        //var_dump($this->session->get('auth-identity'));
		//var_dump(Personas::find());
		//$this->mifaces->addToRend('uno','<uno>uno</uno>', true);
		

		echo $this->view->render('theme',array('pcView'=>'personas/persona','pcData'=>'','jsScript'=>$this->js()));
		
    }
	
	
	function js(){
	
		return	"
				var sortList = [];
				var objs = $('.fwBody .connectedSortable .block');
				
				function sorTear(){
					sortList = [];
					objs.each(
						function(){
							sortList.push({idOb:$(this).data('idob'), col:$(this).parent().data('col'), row:$(this).index()});
						}
					);				
				}
				
				sorTear();
				$('.connectedSortable').sortable({
                connectWith: '.connectedSortable',
                items: '.block',
				forcePlaceholderSize: true,
                opacity: 0.75,
                handle: '.block-title',
                placeholder: 'draggable-placeholder',
                tolerance: 'pointer',
                start: function(e, ui){
					ui.item.startPos = ui.item.parent().data('col') +' - '+ui.item.index();
                    ui.placeholder.css('height', ui.item.outerHeight());
					console.log('Start Div position: ' + ui.item.data('col') +' - '+ui.item.data('row'));
                },
				stop: function(event, ui) {
					ui.item.data('col',ui.item.parent().data('col'));
					ui.item.data('row',ui.item.index());
					console.log('Start position: ' + ui.item.startPos);
					console.log('New position: ' + ui.item.parent().data('col') +' - '+ui.item.index());
					console.log('Div position: ' + ui.item.data('col') +' - '+ui.item.data('row'));
					sorTear();
				}
            }).disableSelection();";
	
	
	}
	
	
	function returnArray(){
	
		return array(
				'head'=>array(
						'nombre'=>'primer'
					),
				'relatedObjects'=>array(
						'1'=>array(
							'widget'=>array(
								'order'=>array(
									'col'=>'0',
									'row'=>'0'
								),
								'name'=>'información<strong>Personal</strong>'
							)
						),
						'2'=>array(
							'widget'=>array(
								'order'=>array(
									'col'=>'1',
									'row'=>'0'
								),
								'name'=>'<strong>RRHH</strong>'
							)
						),
						'3'=>array(
							'widget'=>array(
								'order'=>array(
									'col'=>'1',
									'row'=>'1'
								),
								'name'=>'<strong>OTRO</strong>'
							)
						),
						'4'=>array(
							'widget'=>array(
								'order'=>array(
									'col'=>'2',
									'row'=>'0'
								),
								'name'=>'<strong>Proyectos</strong>'
							)
						)
					)
			);
	
	
	
	}
	
	
	
	
}