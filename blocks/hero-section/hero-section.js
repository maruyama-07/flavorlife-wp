(function (blocks, element, blockEditor, components, i18n) {
    const { registerBlockType } = blocks;
    const { createElement: el, Fragment } = element;
    const { InspectorControls, MediaUpload, MediaUploadCheck, RichText, useBlockProps } = blockEditor;
    const { PanelBody, SelectControl, Button, RadioControl } = components;
    const { __ } = i18n;

    registerBlockType('theme/hero-section', {
        title: __('ヒーローセクション', 'theme'),
        icon: 'format-image',
        category: 'design',
        attributes: {
            backgroundImagePC: {
                type: 'object',
                default: null
            },
            backgroundImageSP: {
                type: 'object',
                default: null
            },
            heroText: {
                type: 'string',
                default: ''
            },
            textColor: {
                type: 'string',
                default: 'white'
            },
            textPosition: {
                type: 'string',
                default: 'center'
            },
            verticalPosition: {
                type: 'string',
                default: 'center'
            },
            sectionHeight: {
                type: 'string',
                default: 'aspect-design'
            }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                backgroundImagePC,
                backgroundImageSP,
                heroText,
                textColor,
                textPosition,
                verticalPosition,
                sectionHeight
            } = attributes;

            // useBlockPropsを使用してブロックのpropsを取得
            const blockProps = useBlockProps ? useBlockProps({
                className: 'hero-section-wrapper'
            }) : { className: 'hero-section-wrapper' };

            const bgStyle = {
                backgroundImage: backgroundImagePC ? 'url(' + backgroundImagePC.url + ')' : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                backgroundSize: 'cover',
                backgroundPosition: 'center',
                minHeight: '300px',
                width: '100%',
                display: 'flex',
                alignItems: verticalPosition === 'top' ? 'flex-start' : verticalPosition === 'bottom' ? 'flex-end' : 'center',
                justifyContent: textPosition === 'left' ? 'flex-start' : textPosition === 'right' ? 'flex-end' : 'center',
                padding: '40px 20px'
            };

            const textStyle = {
                color: textColor === 'white' ? '#ffffff' : '#000000',
                fontSize: '1.8rem',
                fontWeight: 'bold',
                textAlign: 'center',
                maxWidth: '800px',
                width: '100%',
                textShadow: textColor === 'white' ? '2px 2px 4px rgba(0,0,0,0.5)' : '2px 2px 4px rgba(255,255,255,0.5)',
                lineHeight: '1.5',
                padding: '20px'
            };

            return el(
                'div',
                blockProps,
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: '背景画像設定' },
                        el(
                            MediaUploadCheck,
                            {},
                            el(MediaUpload, {
                                onSelect: function (media) {
                                    setAttributes({ backgroundImagePC: media });
                                },
                                allowedTypes: ['image'],
                                value: backgroundImagePC ? backgroundImagePC.id : null,
                                render: function (obj) {
                                    return el(
                                        'div',
                                        {},
                                        el(
                                            Button,
                                            {
                                                onClick: obj.open,
                                                variant: 'secondary',
                                                style: { marginBottom: '10px' }
                                            },
                                            backgroundImagePC ? '背景画像（PC）を変更' : '背景画像（PC）を選択'
                                        ),
                                        backgroundImagePC && el(
                                            'div',
                                            {},
                                            el('img', {
                                                src: backgroundImagePC.url,
                                                alt: 'PC背景',
                                                style: { width: '100%', marginBottom: '10px' }
                                            }),
                                            el(
                                                Button,
                                                {
                                                    onClick: function () {
                                                        setAttributes({ backgroundImagePC: null });
                                                    },
                                                    variant: 'tertiary',
                                                    isDestructive: true
                                                },
                                                '画像を削除'
                                            )
                                        )
                                    );
                                }
                            })
                        ),
                        el(
                            'div',
                            { style: { marginTop: '20px' } },
                            el('p', { style: { fontWeight: 'bold' } }, 'スマホ用背景画像（オプション）'),
                            el(
                                MediaUploadCheck,
                                {},
                                el(MediaUpload, {
                                    onSelect: function (media) {
                                        setAttributes({ backgroundImageSP: media });
                                    },
                                    allowedTypes: ['image'],
                                    value: backgroundImageSP ? backgroundImageSP.id : null,
                                    render: function (obj) {
                                        return el(
                                            'div',
                                            {},
                                            el(
                                                Button,
                                                {
                                                    onClick: obj.open,
                                                    variant: 'secondary',
                                                    style: { marginBottom: '10px' }
                                                },
                                                backgroundImageSP ? '背景画像（SP）を変更' : '背景画像（SP）を選択'
                                            ),
                                            backgroundImageSP && el(
                                                'div',
                                                {},
                                                el('img', {
                                                    src: backgroundImageSP.url,
                                                    alt: 'SP背景',
                                                    style: { width: '100%', marginBottom: '10px' }
                                                }),
                                                el(
                                                    Button,
                                                    {
                                                        onClick: function () {
                                                            setAttributes({ backgroundImageSP: null });
                                                        },
                                                        variant: 'tertiary',
                                                        isDestructive: true
                                                    },
                                                    '画像を削除'
                                                )
                                            )
                                        );
                                    }
                                })
                            )
                        )
                    ),
                    el(
                        PanelBody,
                        { title: 'セクション高さ' },
                        el(SelectControl, {
                            label: '高さ設定',
                            value: sectionHeight,
                            options: [
                                { label: 'デザインカンプ（1200×650 / 750×550）', value: 'aspect-design' },
                                { label: 'アスペクト比 16:9', value: 'aspect-16-9' },
                                { label: 'アスペクト比 21:9（シネマ）', value: 'aspect-21-9' },
                                { label: 'アスペクト比 4:3', value: 'aspect-4-3' },
                                { label: 'アスペクト比 1:1（正方形）', value: 'aspect-1-1' },
                                { label: '画面の高さ 50%', value: '50vh' },
                                { label: '画面の高さ 70%', value: '70vh' },
                                { label: '画面の高さ 100%', value: '100vh' },
                                { label: 'カスタム 500px', value: '500px' },
                                { label: 'カスタム 600px', value: '600px' },
                                { label: 'カスタム 650px', value: '650px' },
                                { label: 'カスタム 800px', value: '800px' },
                                { label: '自動（コンテンツに合わせる）', value: 'auto' }
                            ],
                            onChange: function (value) {
                                setAttributes({ sectionHeight: value });
                            }
                        })
                    ),
                    el(
                        PanelBody,
                        { title: 'テキスト設定' },
                        el(RadioControl, {
                            label: 'テキスト色',
                            selected: textColor,
                            options: [
                                { label: '白', value: 'white' },
                                { label: '黒', value: 'black' }
                            ],
                            onChange: function (value) {
                                setAttributes({ textColor: value });
                            }
                        }),
                        el(RadioControl, {
                            label: '水平位置',
                            selected: textPosition,
                            options: [
                                { label: '左', value: 'left' },
                                { label: '中央', value: 'center' },
                                { label: '右', value: 'right' }
                            ],
                            onChange: function (value) {
                                setAttributes({ textPosition: value });
                            }
                        }),
                        el(RadioControl, {
                            label: '垂直位置',
                            selected: verticalPosition,
                            options: [
                                { label: '上', value: 'top' },
                                { label: '中央', value: 'center' },
                                { label: '下', value: 'bottom' }
                            ],
                            onChange: function (value) {
                                setAttributes({ verticalPosition: value });
                            }
                        })
                    )
                ),
                el(
                    'div',
                    { 
                        className: 'hero-section',
                        style: bgStyle
                    },
                    el(RichText, {
                        tagName: 'div',
                        value: heroText,
                        onChange: function (value) {
                            setAttributes({ heroText: value });
                        },
                        placeholder: 'テキストを入力してください...',
                        style: textStyle
                    })
                )
            );
        },

        save: function () {
            // render.phpを使うのでnullを返す
            return null;
        }
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n
);
