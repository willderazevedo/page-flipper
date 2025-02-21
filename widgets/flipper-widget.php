<?php
class Flipper_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'flipper_widget';
    }

    public function get_title() {
        return __( 'Page Flipper', 'text-domain' );
    }

    public function get_icon() {
        return 'eicon-book';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    public function get_keywords() {
        return [ 'flipper', 'page', 'book', 'pagination' ];
    }

    protected function register_controls() {
        // ===========================
        // 📌 ABA LAYOUT (CONTENT)
        // ===========================

        // 🔍 Configurações da Query
        $this->start_controls_section(
            'section_query',
            [
                'label' => __( 'Query', 'text-domain' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // 🗂️ Criando as abas "Include" e "Exclude"
        $this->start_controls_tabs('query_tabs');

        // 🟢 Aba "Include"
        $this->start_controls_tab(
            'include_tab',
            [
                'label' => __( 'Include', 'text-domain' ),
            ]
        );

        $this->add_control(
            'include_terms',
            [
                'label'    => __( 'Include Terms', 'text-domain' ),
                'type'     => \Elementor\Controls_Manager::SELECT2,
                'options'  => \Elementor\Plugin::$instance->controls_manager->get_control_options_from_taxonomy( 'page_flipper_category' ),
                'multiple' => true,
            ]
        );

        $this->end_controls_tab();

        // 🔴 Aba "Exclude"
        $this->start_controls_tab(
            'exclude_tab',
            [
                'label' => __( 'Exclude', 'text-domain' ),
            ]
        );

        $this->add_control(
            'exclude_terms',
            [
                'label'    => __( 'Exclude Terms', 'text-domain' ),
                'type'     => \Elementor\Controls_Manager::SELECT2,
                'options'  => \Elementor\Plugin::$instance->controls_manager->get_control_options_from_taxonomy( 'page_flipper_category' ),
                'multiple' => true,
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        // 🔃 Ordem (ASC/DESC)
        $this->add_control(
            'order',
            [
                'label'   => __( 'Order', 'text-domain' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC'  => __( 'Ascending', 'text-domain' ),
                    'DESC' => __( 'Descending', 'text-domain' ),
                ],
            ]
        );

        // 🔀 Ordenar Por
        $this->add_control(
            'orderby',
            [
                'label'   => __( 'Order By', 'text-domain' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date'       => __( 'Date', 'text-domain' ),
                    'title'      => __( 'Title', 'text-domain' ),
                    'rand'       => __( 'Random', 'text-domain' )
                ],
            ]
        );

        $this->end_controls_section();


        // 🔧 Configurações Gerais (Settings)
        $this->start_controls_section(
            'section_settings',
            [
                'label' => __( 'Settings', 'text-domain' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // 🔘 Habilitar Sumário
        $this->add_control(
            'enable_summary',
            [
                'label'        => __( 'Enable Summary', 'text-domain' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'text-domain' ),
                'label_off'    => __( 'No', 'text-domain' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        // 🔘 Habilitar Barra de Ações
        $this->add_control(
            'enable_action_bar',
            [
                'label'        => __( 'Enable Action Bar', 'text-domain' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'text-domain' ),
                'label_off'    => __( 'No', 'text-domain' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        // 🔘 Habilitar Controles de Navegação
        $this->add_control(
            'enable_controls',
            [
                'label'        => __( 'Enable Navigation Controls', 'text-domain' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'text-domain' ),
                'label_off'    => __( 'No', 'text-domain' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->end_controls_section();

        // ===========================
        // 🎨 ABA ESTILO (STYLE)
        // ===========================

        $this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Style', 'text-domain' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // 🎨 Cor de fundo da barra de ações
        $this->add_control(
            'action_bar_bg_color',
            [
                'label'     => __( 'Action Bar Background Color', 'text-domain' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .flipper-action-bar' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // 🎨 Cor de fundo do sumário
        $this->add_control(
            'summary_bg_color',
            [
                'label'     => __( 'Summary Background Color', 'text-domain' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .flipper-summary' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // 🎨 Cor dos ícones dos botões de controle
        $this->add_control(
            'controls_icon_color',
            [
                'label'     => __( 'Controls Icon Color', 'text-domain' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .flipper-controls button i' => 'color: {{VALUE}};',
                ],
            ]
        );

        // 🖋️ Tipografia completa
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'content_typography',
                'label'    => __( 'Typography', 'text-domain' ),
                'selector' => '{{WRAPPER}} .flipper-widget',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $languages = implode(',', $settings['languages']);

        echo '<div id="google-translate-widget" data-languages="' . esc_attr( $languages ) . '"></div>';
    }
}