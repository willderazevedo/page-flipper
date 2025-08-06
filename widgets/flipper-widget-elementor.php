<?php
class Wa_Page_Flipper_Widget_Elementor extends \Elementor\Widget_Base {
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

        // 🔘 Habilitar Posts Relacionados
        $this->add_control(
            'enable_related',
            [
                'label'        => __( 'Enable Related', 'page-flipper' ),
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

        // 🔘 Habilitar Compartilhamento
        $this->add_control(
            'enable_share',
            [
                'label'        => __( 'Enable Share', 'page-flipper' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'page-flipper' ),
                'label_off'    => __( 'No', 'page-flipper' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        // 🔘 Habilitar Zoom
        $this->add_control(
            'enable_zoom',
            [
                'label'        => __( 'Enable Zoom', 'page-flipper' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'page-flipper' ),
                'label_off'    => __( 'No', 'page-flipper' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        // 🔘 Habilitar imagem como Background
        $this->add_control(
            'enable_background_image',
            [
                'label'        => __( 'Enable Background Image', 'page-flipper' ),
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
            'page_background_color',
            [
                'label'     => __( 'Page Background Color', 'page-flipper' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#333333'
            ]
        );

        // 🎨 Cor de fundo da barra de ações
        $this->add_control(
            'page_surface_color',
            [
                'label'     => __( 'Page Surface Color', 'page-flipper' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => 'rgba(0, 0, 0, 0.4)'
            ]
        );

        // 🎨 Cor de fundo da barra de ações
        $this->add_control(
            'page_surface_accent_color',
            [
                'label'     => __( 'Page Surface Accent Color', 'page-flipper' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff'
            ]
        );

        // 🎨 Cor de fundo do sumário
        $this->add_control(
            'page_accent_color',
            [
                'label'     => __( 'Page Accent Color', 'page-flipper' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#eac101'
            ]
        );

        // 🎨 Cor dos ícones dos botões de controle
        $this->add_control(
            'page_font_color',
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
                'enable_summary=' . esct_attr($settings['enable_summary']) .
                'enable_related=' . esct_attr($settings['enable_related']) .
                'enable_controls=' . esct_attr($settings['enable_controls']) .
                'enable_share=' . esct_attr($settings['enable_share']) .
                'enable_zoom=' . esct_attr($settings['enable_zoom']) .
                'enable_background_image=' . esct_attr($settings['enable_background_image']) .
                'page_background_color=' . esct_attr($settings['page_background_color']) .
                'page_surface_color=' . esct_attr($settings['page_surface_color']) .
                'page_surface_accent_color=' . esct_attr($settings['page_surface_accent_color']) .
                'page_accent_color=' . esct_attr($settings['page_accent_color']) .
                'page_font_color=' . esct_attr($settings['page_font_color']) .
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