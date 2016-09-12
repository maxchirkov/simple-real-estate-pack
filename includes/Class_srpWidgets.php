<?php

class srpWidgets
{
    public $widgets = array();

    function __construct(){}

    function add($atts){
      //Default $atts
      $default_atts = array(
          'name'        => NULL,
          'title'       => NULL,
          'tab_name'    => NULL,
          'content'     => NULL,
          'callback_function' => NULL,
          'init_function'     => NULL,
          'ajax'        => false,
          'save_to_buffer'    => false,
      );
      //use shortcodes API logic http://codex.wordpress.org/Shortcode_API
      $merged_atts = shortcode_atts($default_atts, $atts);
      extract( $merged_atts, EXTR_REFS );

      //$name is required
      if ( NULL == $name )
        return;

      $this->widgets[$name] = new srpWidget($name, $title, $tab_name, $content, $callback_function, $init_function, $ajax, $save_to_buffer);
      $tab = new srpTab($name, $title, $tab_name);
      $this->widgets[$name]->tab = $tab->tab;
    }

    function add_title($name, $title){
        $this->widgets[$name]->title = $title;
    }

    function add_content($name, $content){
        $this->widgets[$name]->content = $content;
    }

    function add_tab($name, $title, $tab_name, $before='<li>', $after='</li>'){
        $tab = new srpTab($name, $title, $before, $after);
        $this->widgets[$name]->tab = $tab->tab;
    }

    function remove($name){
        unset($this->widgets[$name]);
    }

    function get_widget($name){
        return $this->widgets[$name];
    }

    function the_widget($name){
        echo $this->widgets[$name]->title;
        echo $this->widgets[$name]->content;
    }

    function get_all(){
        global $srp_ext_gre_content, $srp_ext_gre_tabs;

        if(count($this->widgets) < 1)
                return;

        $content = '';
        foreach($this->widgets as $widget){
            if(in_array($widget->name, $srp_ext_gre_content)){
                $content .= '<div id="srp_'.$widget->name.'_tab" class="clearfix">';

                if(!empty($srp_ext_gre_tabs[$widget->name]['heading'])){
                    $content .= '<h2><span>' . $srp_ext_gre_tabs[$widget->name]['heading'] . '</span></h2>';
                }else
                if($widget->title){
                    $content .= '<h2><span>' . $widget->title . '</span></h2>';
                }
                if(!function_exists($widget->callback_function))
                        return;
                if($widget->save_to_buffer == true){
                    $content .= srp_buffer($widget->callback_function);
                }else{
                    $content .= call_user_func($widget->callback_function);
                }
                $content .= '</div>';
            }
        }
        return $content;
    }

    function get_all_ajax($ajax){
        global $srp_ext_gre_content, $srp_ext_gre_tabs;

        if(count($this->widgets) < 1 || empty($srp_ext_gre_content))
                return;
        foreach($this->widgets as $widget){
            if(in_array($widget->name, $srp_ext_gre_content)){
                if($widget->ajax === $ajax){
                    $nonajax[] = $widget;
                }
            }
        }

        if(!is_array($nonajax))
            return;

        $content = '';
        foreach($nonajax as $widget){
            $content .= '<div id="srp_'.$widget->name.'_tab" class="clearfix">';

            if(!empty($srp_ext_gre_tabs[$widget->name]['heading'])){
                $content .= '<h2><span>' . $srp_ext_gre_tabs[$widget->name]['heading'] . '</span></h2>';
            }elseif($widget->title){
                $content .= '<h2><span>' . $widget->title . '</span></h2>';
            }

            if($widget->save_to_buffer == true){
                $content .= srp_buffer($widget->callback_function);
            }else{
                $content .= call_user_func($widget->callback_function);
            }
            $content .= '</div>';
        }
        return $content;
    }

    function print_all(){
        global $srp_ext_gre_content, $srp_ext_gre_tabs;

        if(count($this->widgets) < 1)
                return;

        foreach($this->widgets as $widget){
            if(!empty($srp_ext_gre_tabs[$widget->name]['heading'])){
                $content = '<h2><span>' . $srp_ext_gre_tabs[$widget->name]['heading'] . '</span></h2>';
            }else
            if($widget->title){
                $content = '<h2><span>' . $widget->title . '</span></h2>';
            }

            $content .= $widget->content;
            if($widget->tab){
                echo '<div id="srp_'.$widget->name.'_tab" class="clearfix">';
                echo $content;
                echo '</div>';
            }
        }
    }

    function print_widget($name){
        global $srp_ext_gre_content, $srp_ext_gre_tabs;

        if(!$this->widgets[$name])
                return;

          $widget = $this->widgets[$name];
            $content = '<div id="srp_'.$widget->name.'_tab" class="clearfix">';

            if(!empty($srp_ext_gre_tabs[$widget->name]['heading'])){
                $content .= '<h2><span>' . $srp_ext_gre_tabs[$widget->name]['heading'] . '</span></h2>';
            }else
            if($widget->title){
                $content .= '<h2><span>' . $widget->title . '</span></h2>';
            }

            if($widget->save_to_buffer == true){
                $content .= srp_buffer($widget->callback_function);
            }else{
                $content .= call_user_func($widget->callback_function);
            }
            $content .= '</div>';
            echo $content;
    }

    function get_tabs(){
        global $srp_ext_gre_content, $srp_ext_gre_tabs;

        if(count($this->widgets) < 1 || empty($srp_ext_gre_content))
                return;

        $tabs = false;
        foreach($this->widgets as $widget){
            if(in_array($widget->name, $srp_ext_gre_content)){
                if(!empty($srp_ext_gre_tabs[$widget->name]['tabname'])){
                   $tabs .= '<li>' . '<a href="#srp_'. $widget->name . '_tab" title="'.__($widget->title, 'simplerealestatepack') . '"><span>' . __($srp_ext_gre_tabs[$widget->name]['tabname'], 'simplerealestatepack') . '</span></a>' . '</li>';
                }else{
                    $tabs .= $widget->tab;
                }
            }
        }
        if($tabs){
            $content = '<ul class="clearfix">';
            $content .= $tabs;
            $content .= '</ul>';
            return $content;
        }
    }

    function print_tabs(){
        echo $this->get_tabs();
    }
}

Class srpWidget{
    var $name;
    var $title;
    var $tab_name;
    var $content;
    var $callback_function;
    var $init_function;
    var $ajax;
    var $save_to_buffer;

    function __construct($name, $title=NULL, $tab_name=NULL, $content=NULL, $callback_function=NULL, $init_function=NULL, $ajax=false, $save_to_buffer=false){
        $this->name = $name;
        $this->title = $title;
        $this->content = $content;
        $this->callback_function = $callback_function;
        $this->init_function = $init_function;
        $this->ajax = $ajax;
        $this->save_to_buffer = $save_to_buffer;
    }
}

Class srpTab{
    var $tab;

    function __construct($name, $title, $tab_name, $before='<li>', $after='</li>'){
        $this->tab = $before . '<a href="#srp_'. $name . '_tab" title="'.__($title, 'simplerealestatepack') . '"><span>' . __($tab_name, 'simplerealestatepack') . '</span></a>' . $after;
    }
}