<?php
class Flipper_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'flipper_widget';
    }

    public function get_title() {
        return __( 'Page Flipper', 'page-flipper' );
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
                'label' => __( 'Query', 'page-flipper' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // 🗂️ Criando as abas "Include" e "Exclude"
        $this->start_controls_tabs('query_tabs');

        // 🟢 Aba "Include"
        $this->start_controls_tab(
            'include_tab',
            [
                'label' => __( 'Include', 'page-flipper' ),
            ]
        );

        $this->add_control(
            'include_terms',
            [
                'label'    => __( 'Include Terms', 'page-flipper' ),
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
                'label' => __( 'Exclude', 'page-flipper' ),
            ]
        );

        $this->add_control(
            'exclude_terms',
            [
                'label'    => __( 'Exclude Terms', 'page-flipper' ),
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
                'label'   => __( 'Order', 'page-flipper' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC'  => __( 'Ascending', 'page-flipper' ),
                    'DESC' => __( 'Descending', 'page-flipper' ),
                ],
            ]
        );

        // 🔀 Ordenar Por
        $this->add_control(
            'orderby',
            [
                'label'   => __( 'Order By', 'page-flipper' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date'       => __( 'Date', 'page-flipper' ),
                    'title'      => __( 'Title', 'page-flipper' ),
                    'rand'       => __( 'Random', 'page-flipper' )
                ],
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

        // 🎨 Cor de fundo da barra de ações
        $this->add_control(
            'action_bar_bg_color',
            [
                'label'     => __( 'Action Bar Background Color', 'page-flipper' ),
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
                'label'     => __( 'Summary Background Color', 'page-flipper' ),
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
                'label'     => __( 'Controls Icon Color', 'page-flipper' ),
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
                'label'    => __( 'Typography', 'page-flipper' ),
                'selector' => '{{WRAPPER}} .flipper-widget',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $args = [
            'post_type'      => 'page_flipper',
            'posts_per_page' => 1,
            'order'          => $settings['order'],
            'orderby'        => $settings['orderby'],
        ];

        if ( ! empty( $settings['include_terms'] ) ) {
            $args['tax_query'][] = [
                'taxonomy' => 'page_flipper_category',
                'field'    => 'term_id',
                'terms'    => $settings['include_terms'],
                'operator' => 'IN',
            ];
        }

        if ( ! empty( $settings['exclude_terms'] ) ) {
            $args['tax_query'][] = [
                'taxonomy' => 'page_flipper_category',
                'field'    => 'term_id',
                'terms'    => $settings['exclude_terms'],
                'operator' => 'NOT IN',
            ];
        }

        $query = new WP_Query($args);

        if ( $query->have_posts() ) {
            echo '<div class="flipper-widget">';
            
            if ( 'yes' === $settings['enable_summary'] ) {
                echo '<div class="flipper-summary"><p>Summary of the page</p></div>';
            }

            while ( $query->have_posts() ) {
                $query->the_post();
                echo '<div class="flipper-item">';
                echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
                echo '</div>';
            }

            if ( 'yes' === $settings['enable_action_bar'] ) {
                echo '<div class="flipper-action-bar"><button>Bookmark</button></div>';
            }

            if ( 'yes' === $settings['enable_controls'] ) {
                echo '<div class="flipper-controls">';
                echo '<button class="prev">← Previous</button>';
                echo '<button class="next">Next →</button>';
                echo '</div>';
            }

            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>' . __( 'No pages found.', 'page-flipper' ) . '</p>';
        }
    }
}