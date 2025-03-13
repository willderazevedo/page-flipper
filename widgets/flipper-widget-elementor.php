<?php
class Flipper_Widget_Elementor extends \Elementor\Widget_Base {
    public function get_name() {
        return 'flipper_widget';
    }

    public function get_title() {
        return __( 'Page Flipper', 'page-flipper' );
    }

    public function get_icon() {
        return 'eicon-document-file';
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
                'label' => __( 'Query', 'page-flipper' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // 🔍 Registro a ser exibido
        $this->add_control(
            'flipper_query',
            [
                'label'        => __( 'Select a Page Flipper', 'page-flipper' ),
                'type'         => \Elementor\Controls_Manager::SELECT2,
                'label_block'  => true,
                'options'      => $this->get_page_flipper_options(),
                'default'      => '',
                'description'  => __('Leave empty to use Current Query', 'page-flipper'),
            ]
        );

        $this->end_controls_section();


        // 🔧 Configurações Gerais (Settings)
        $this->start_controls_section(
            'section_settings',
            [
                'label' => __( 'Settings', 'page-flipper' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // 🔘 Habilitar Sumário
        $this->add_control(
            'enable_summary',
            [
                'label'        => __( 'Enable Summary', 'page-flipper' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'page-flipper' ),
                'label_off'    => __( 'No', 'page-flipper' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        // 🔘 Habilitar Barra de Ações
        $this->add_control(
            'enable_action_bar',
            [
                'label'        => __( 'Enable Action Bar', 'page-flipper' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'page-flipper' ),
                'label_off'    => __( 'No', 'page-flipper' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        // 🔘 Habilitar Controles de Navegação
        $this->add_control(
            'enable_controls',
            [
                'label'        => __( 'Enable Navigation Controls', 'page-flipper' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'page-flipper' ),
                'label_off'    => __( 'No', 'page-flipper' ),
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
                'label' => __( 'Style', 'page-flipper' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // 🎨 Cor de fundo da página
        $this->add_control(
            'page_bg_color',
            [
                'label'     => __( 'Page Background Color', 'page-flipper' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#333333'
            ]
        );

        // 🎨 Cor de fundo da barra de ações
        $this->add_control(
            'action_bar_bg_color',
            [
                'label'     => __( 'Action Bar Background Color', 'page-flipper' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#555555'
            ]
        );

        // 🎨 Cor de fundo do sumário
        $this->add_control(
            'summary_bg_color',
            [
                'label'     => __( 'Summary Background Color', 'page-flipper' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#555555'
            ]
        );

        // 🎨 Cor dos ícones dos botões de controle
        $this->add_control(
            'controls_icon_color',
            [
                'label'     => __( 'Controls Icon Color', 'page-flipper' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff'
            ]
        );

        // 🎨 Cor dos ícones dos botões de controle
        $this->add_control(
            'font_color',
            [
                'label'     => __( 'Font Color', 'page-flipper' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff'
            ]
        );

        // 🖋️ Tipografia completa
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'content_typography',
                'label'    => __( 'Typography', 'page-flipper' ),
                'selector' => '{{WRAPPER}} .flipper-widget-wrapper',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings   = $this->get_settings_for_display();
        $flipper_id = !empty($settings['flipper_query']) ? $settings['flipper_query'] : get_the_ID();
    
        echo do_shortcode(
            '[page_flipper ' .
                'id="' . esc_attr($flipper_id) . '" ' .
                'summary="' . esc_attr($settings['enable_summary']) . '" ' .
                'action_bar="' . esc_attr($settings['enable_action_bar']) . '" ' .
                'controls="' . esc_attr($settings['enable_controls']) . '" ' .
                'page_bg="' . esc_attr($settings['page_bg_color']) . '" ' .
                'action_bar_bg="' . esc_attr($settings['action_bar_bg_color']) . '" ' .
                'summary_bg="' . esc_attr($settings['summary_bg_color']) . '" ' .
                'controls_icon="' . esc_attr($settings['controls_icon_color']) . '" ' .
                'font_color="' . esc_attr($settings['font_color']) . '" ' .
            ']'
        );
    }

    private function get_page_flipper_options() {
        $posts = get_posts([
            'post_type'      => 'page_flipper',
            'posts_per_page' => -1,
        ]);
    
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $options[$post->ID] = $post->post_title;
            }
        }
    
        return $options;
    }
}