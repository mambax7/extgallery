<?php
/**
 * ExtGallery Admin settings
 * Manage admin pages
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright   {@link https://xoops.org/ XOOPS Project}
 * @license     GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author      Zoullou (http://www.zoullou.net)
 * @package     ExtGallery
 */
require_once __DIR__ . '/admin_header.php';

xoops_load('XoopsFormLoader');

if (\Xmf\Request::hasVar('op', 'GET')) {
    $op = $_GET['op'];
} else {
    $op = 'default';
}

if (\Xmf\Request::hasVar('step', 'POST')) {
    $step = $_POST['step'];
} else {
    $step = 'default';
}

switch ($op) {
    case 'uploadfont':

        switch ($step) {
            case 'enreg':

                $uploaddir  = XOOPS_ROOT_PATH . '/modules/extgallery/assets/fonts/';
                $uploadfile = $uploaddir . basename($_FILES['font_file']['name']);

                if (file_exists($uploadfile)) {
                    echo 'The font is already present on the server.';
                }

                move_uploaded_file($_FILES['font_file']['tmp_name'], $uploadfile);

                redirect_header('watermark-border.php', 3, _AM_EXTGALLERY_FONT_ADDED);

                break;
            case 'default':

            default:

                xoops_cp_header();

                $xoopsTpl->assign('uploadfont', true);

                $fonts = [];

                $rep = XOOPS_ROOT_PATH . '/modules/extgallery/assets/fonts/';
                $dir = opendir($rep);
                while (false !== ($f = readdir($dir))) {
                    if (is_file($rep . $f)) {
                        if (preg_match('/.*ttf/', mb_strtolower($f))) {
                            $fonts[] = $f;
                        }
                    }
                }

                $xoopsTpl->assign('fonts', $fonts);

                $form = new \XoopsThemeForm(_AM_EXTGALLERY_ADD_FONT, 'add_font', 'watermark-border.php?op=uploadfont', 'post', true);
                $form->setExtra('enctype="multipart/form-data"');
                $form->addElement(new \XoopsFormFile(_AM_EXTGALLERY_FONT_FILE, 'font_file', get_cfg_var('upload_max_filesize') * 1024 * 1024), false);
                $form->addElement(new \XoopsFormHidden('step', 'enreg'));
                $form->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

                $xoopsTpl->assign('fontform', $form->display());

                // Call template file
                $xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/extgallery/templates/admin/extgallery_admin_watermark_border.tpl');
                xoops_cp_footer();

                break;
        }

        break;
    case 'conf':

        switch ($step) {
            case 'enreg':

                /** @var \XoopsModuleHandler $moduleHandler */
                /** @var \XoopsConfigHandler $configHandler */
                $configHandler    = xoops_getHandler('config');
                $moduleIdCriteria = new \Criteria('conf_modid', $xoopsModule->getVar('mid'));

                // Param for applied to the test photo
                $testParam                       = [];
                $testParam['watermark_type']     = $helper->getConfig('watermark_font');
                $testParam['watermark_font']     = $helper->getConfig('watermark_font');
                $testParam['watermark_text']     = $helper->getConfig('watermark_text');
                $testParam['watermark_position'] = $helper->getConfig('watermark_position');
                $testParam['watermark_color']    = $helper->getConfig('watermark_color');
                $testParam['watermark_fontsize'] = $helper->getConfig('watermark_fontsize');
                $testParam['watermark_padding']  = $helper->getConfig('watermark_padding');
                $testParam['inner_border_color'] = $helper->getConfig('inner_border_color');
                $testParam['inner_border_size']  = $helper->getConfig('inner_border_size');
                $testParam['outer_border_color'] = $helper->getConfig('outer_border_color');
                $testParam['outer_border_size']  = $helper->getConfig('outer_border_size');

                if (\Xmf\Request::hasVar('watermark_font', 'POST')) {
                    $testParam['watermark_font'] = $_POST['watermark_font'];
                    if ($helper->getConfig('watermark_font') != $_POST['watermark_font']) {
                        $criteria = new \CriteriaCompo();
                        $criteria->add($moduleIdCriteria);
                        $criteria->add(new \Criteria('conf_name', 'watermark_font'));
                        $config      = $configHandler->getConfigs($criteria);
                        $config      = $config[0];
                        $configValue = [
                            'conf_modid'     => $xoopsModule->getVar('mid'),
                            'conf_catid'     => 0,
                            'conf_name'      => 'watermark_font',
                            'conf_value'     => $_POST['watermark_font'],
                            'conf_formtype'  => 'hidden',
                            'conf_valuetype' => 'text',
                        ];
                        $config->setVars($configValue);
                        $configHandler->insertConfig($config);
                    }

                    $testParam['watermark_type'] = $_POST['watermark_type'];
                    if ($helper->getConfig('watermark_type') != $_POST['watermark_type']) {
                        $criteria = new \CriteriaCompo();
                        $criteria->add($moduleIdCriteria);
                        $criteria->add(new \Criteria('conf_name', 'watermark_type'));
                        $config      = $configHandler->getConfigs($criteria);
                        $config      = $config[0];
                        $configValue = [
                            'conf_modid'     => $xoopsModule->getVar('mid'),
                            'conf_catid'     => 0,
                            'conf_name'      => 'watermark_type',
                            'conf_value'     => $_POST['watermark_type'],
                            'conf_formtype'  => 'hidden',
                            'conf_valuetype' => 'int',
                        ];
                        $config->setVars($configValue);
                        $configHandler->insertConfig($config);
                    }

                    if (\Xmf\Request::hasVar('watermark_text', 'POST')) {
                        $testParam['watermark_text'] = $_POST['watermark_text'];
                        if ($helper->getConfig('watermark_text') != $_POST['watermark_text']) {
                            $criteria = new \CriteriaCompo();
                            $criteria->add($moduleIdCriteria);
                            $criteria->add(new \Criteria('conf_name', 'watermark_text'));
                            $config      = $configHandler->getConfigs($criteria);
                            $config      = $config[0];
                            $configValue = [
                                'conf_modid'     => $xoopsModule->getVar('mid'),
                                'conf_catid'     => 0,
                                'conf_name'      => 'watermark_text',
                                'conf_value'     => $_POST['watermark_text'],
                                'conf_formtype'  => 'hidden',
                                'conf_valuetype' => 'text',
                            ];
                            $config->setVars($configValue);
                            $configHandler->insertConfig($config);
                        }
                    }

                    $testParam['watermark_position'] = $_POST['watermark_position'];
                    if ($helper->getConfig('watermark_position') != $_POST['watermark_position']) {
                        $criteria = new \CriteriaCompo();
                        $criteria->add($moduleIdCriteria);
                        $criteria->add(new \Criteria('conf_name', 'watermark_position'));
                        $config      = $configHandler->getConfigs($criteria);
                        $config      = $config[0];
                        $configValue = [
                            'conf_modid'     => $xoopsModule->getVar('mid'),
                            'conf_catid'     => 0,
                            'conf_name'      => 'watermark_position',
                            'conf_value'     => $_POST['watermark_position'],
                            'conf_formtype'  => 'hidden',
                            'conf_valuetype' => 'text',
                        ];
                        $config->setVars($configValue);
                        $configHandler->insertConfig($config);
                    }

                    $testParam['watermark_color'] = $_POST['watermark_color'];
                    if ($helper->getConfig('watermark_color') != $_POST['watermark_color']) {
                        $criteria = new \CriteriaCompo();
                        $criteria->add($moduleIdCriteria);
                        $criteria->add(new \Criteria('conf_name', 'watermark_color'));
                        $config      = $configHandler->getConfigs($criteria);
                        $config      = $config[0];
                        $configValue = [
                            'conf_modid'     => $xoopsModule->getVar('mid'),
                            'conf_catid'     => 0,
                            'conf_name'      => 'watermark_color',
                            'conf_value'     => $_POST['watermark_color'],
                            'conf_formtype'  => 'hidden',
                            'conf_valuetype' => 'text',
                        ];
                        $config->setVars($configValue);
                        $configHandler->insertConfig($config);
                    }

                    $testParam['watermark_fontsize'] = $_POST['watermark_fontsize'];
                    if ($helper->getConfig('watermark_fontsize') != $_POST['watermark_fontsize']) {
                        $criteria = new \CriteriaCompo();
                        $criteria->add($moduleIdCriteria);
                        $criteria->add(new \Criteria('conf_name', 'watermark_fontsize'));
                        $config      = $configHandler->getConfigs($criteria);
                        $config      = $config[0];
                        $configValue = [
                            'conf_modid'     => $xoopsModule->getVar('mid'),
                            'conf_catid'     => 0,
                            'conf_name'      => 'watermark_fontsize',
                            'conf_value'     => $_POST['watermark_fontsize'],
                            'conf_formtype'  => 'hidden',
                            'conf_valuetype' => 'int',
                        ];
                        $config->setVars($configValue);
                        $configHandler->insertConfig($config);
                    }

                    $testParam['watermark_padding'] = $_POST['watermark_padding'];
                    if ($helper->getConfig('watermark_padding') != $_POST['watermark_padding']) {
                        $criteria = new \CriteriaCompo();
                        $criteria->add($moduleIdCriteria);
                        $criteria->add(new \Criteria('conf_name', 'watermark_padding'));
                        $config      = $configHandler->getConfigs($criteria);
                        $config      = $config[0];
                        $configValue = [
                            'conf_modid'     => $xoopsModule->getVar('mid'),
                            'conf_catid'     => 0,
                            'conf_name'      => 'watermark_padding',
                            'conf_value'     => $_POST['watermark_padding'],
                            'conf_formtype'  => 'hidden',
                            'conf_valuetype' => 'int',
                        ];
                        $config->setVars($configValue);
                        $configHandler->insertConfig($config);
                    }
                }

                if (\Xmf\Request::hasVar('inner_border_color', 'POST')) {
                    $testParam['inner_border_color'] = $_POST['inner_border_color'];
                    if ($helper->getConfig('inner_border_color') != $_POST['inner_border_color']) {
                        $criteria = new \CriteriaCompo();
                        $criteria->add($moduleIdCriteria);
                        $criteria->add(new \Criteria('conf_name', 'inner_border_color'));
                        $config      = $configHandler->getConfigs($criteria);
                        $config      = $config[0];
                        $configValue = [
                            'conf_modid'     => $xoopsModule->getVar('mid'),
                            'conf_catid'     => 0,
                            'conf_name'      => 'inner_border_color',
                            'conf_value'     => $_POST['inner_border_color'],
                            'conf_formtype'  => 'hidden',
                            'conf_valuetype' => 'text',
                        ];
                        $config->setVars($configValue);
                        $configHandler->insertConfig($config);
                    }

                    $testParam['inner_border_size'] = $_POST['inner_border_size'];
                    if ($helper->getConfig('inner_border_size') != $_POST['inner_border_size']) {
                        $criteria = new \CriteriaCompo();
                        $criteria->add($moduleIdCriteria);
                        $criteria->add(new \Criteria('conf_name', 'inner_border_size'));
                        $config      = $configHandler->getConfigs($criteria);
                        $config      = $config[0];
                        $configValue = [
                            'conf_modid'     => $xoopsModule->getVar('mid'),
                            'conf_catid'     => 0,
                            'conf_name'      => 'inner_border_size',
                            'conf_value'     => $_POST['inner_border_size'],
                            'conf_formtype'  => 'hidden',
                            'conf_valuetype' => 'int',
                        ];
                        $config->setVars($configValue);
                        $configHandler->insertConfig($config);
                    }

                    $testParam['outer_border_color'] = $_POST['outer_border_color'];
                    if ($helper->getConfig('outer_border_color') != $_POST['outer_border_color']) {
                        $criteria = new \CriteriaCompo();
                        $criteria->add($moduleIdCriteria);
                        $criteria->add(new \Criteria('conf_name', 'outer_border_color'));
                        $config      = $configHandler->getConfigs($criteria);
                        $config      = $config[0];
                        $configValue = [
                            'conf_modid'     => $xoopsModule->getVar('mid'),
                            'conf_catid'     => 0,
                            'conf_name'      => 'outer_border_color',
                            'conf_value'     => $_POST['outer_border_color'],
                            'conf_formtype'  => 'hidden',
                            'conf_valuetype' => 'text',
                        ];
                        $config->setVars($configValue);
                        $configHandler->insertConfig($config);
                    }

                    $testParam['outer_border_size'] = $_POST['outer_border_size'];
                    if ($helper->getConfig('outer_border_size') != $_POST['outer_border_size']) {
                        $criteria = new \CriteriaCompo();
                        $criteria->add($moduleIdCriteria);
                        $criteria->add(new \Criteria('conf_name', 'outer_border_size'));
                        $config      = $configHandler->getConfigs($criteria);
                        $config      = $config[0];
                        $configValue = [
                            'conf_modid'     => $xoopsModule->getVar('mid'),
                            'conf_catid'     => 0,
                            'conf_name'      => 'outer_border_size',
                            'conf_value'     => $_POST['outer_border_size'],
                            'conf_formtype'  => 'hidden',
                            'conf_valuetype' => 'int',
                        ];
                        $config->setVars($configValue);
                        $configHandler->insertConfig($config);
                    }
                }

                // Refresh the photo exemple

                require_once XOOPS_ROOT_PATH . '/modules/extgallery/class/pear/Image/Transform.php';

                // Loading original image
                // Define Graphical library path
                if ('imagick' === $helper->getConfig('graphic_lib')) {
                    define('IMAGE_TRANSFORM_IM_PATH', $helper->getConfig('graphic_lib_path'));
                }
                $newImageTransform = new \Image_Transform();
                //                $imageTransform = Image_Transform::factory($helper->getConfig('graphic_lib'));
                $imageTransform = $newImageTransform->factory($helper->getConfig('graphic_lib'));
                $imageTransform->load('../assets/images/watermark-border-orig.jpg');

                // Making Watermark
                if ('tl' === $testParam['watermark_position']) {
                    $x = 0;
                    $y = 0;
                } elseif ('tr' === $testParam['watermark_position']) {
                    $x = -1;
                    $y = 0;
                } elseif ('bl' === $testParam['watermark_position']) {
                    $x = 0;
                    $y = -1;
                } elseif ('br' === $testParam['watermark_position']) {
                    $x = -1;
                    $y = -1;
                } elseif ('tc' === $testParam['watermark_position']) {
                    $x = 1;
                    $y = 0;
                } elseif ('bc' === $testParam['watermark_position']) {
                    $x = 1;
                    $y = -1;
                } elseif ('lc' === $testParam['watermark_position']) {
                    $x = 0;
                    $y = 1;
                } elseif ('rc' === $testParam['watermark_position']) {
                    $x = -1;
                    $y = 1;
                } elseif ('cc' === $testParam['watermark_position']) {
                    $x = 1;
                    $y = 1;
                }

                $text = (0 == $testParam['watermark_type']) ? $GLOBALS['xoopsUser']->getVar('uname') : $testParam['watermark_text'];

                $watermarkParams = [
                    'text'         => $text,
                    'x'            => $x,
                    'y'            => $y,
                    'color'        => $testParam['watermark_color'],
                    'font'         => '../assets/fonts/' . $testParam['watermark_font'],
                    'size'         => $testParam['watermark_fontsize'],
                    'resize_first' => false,
                    'padding'      => $testParam['watermark_padding'],
                ];
                $imageTransform->addText($watermarkParams);

                // Making border
                $borders   = [];
                $borders[] = [
                    'borderWidth' => $testParam['inner_border_size'],
                    'borderColor' => $testParam['inner_border_color'],
                ];
                $borders[] = [
                    'borderWidth' => $testParam['outer_border_size'],
                    'borderColor' => $testParam['outer_border_color'],
                ];

                foreach ($borders as $border) {
                    $imageTransform->addBorder($border['borderWidth'], $border['borderColor']);
                }

                // Remove old test image
                deleteImageTest();
                // Saving transformation on test image
                $imageTransform->save('../assets/images/watermark-border-test-' . mb_substr(md5(uniqid(mt_rand(), true)), 27) . '.jpg');
                $imageTransform->free();

                redirect_header('watermark-border.php', 3, _AM_EXTGALLERY_CONFIGURATION_SAVED);

                break;
        }

        break;
    case 'default':

    default:

        xoops_cp_header();

        $nbFonts = 0;
        $fonts   = [];

        $rep = XOOPS_ROOT_PATH . '/modules/extgallery/assets/fonts/';
        $dir = opendir($rep);
        while (false !== ($f = readdir($dir))) {
            if (is_file($rep . $f)) {
                if (preg_match('/.*ttf/', mb_strtolower($f))) {
                    ++$nbFonts;
                    $fonts[] = $f;
                }
            }
        }

        $xoopsTpl->assign('nbfonts', sprintf(_AM_EXTGALLERY_ADD_FONT_LINK, $nbFonts));

        $xoopsTpl->assign('fonts', $fonts);

        // Display Watermark param form if FreeType is supported
        if (function_exists('imagettfbbox')) {
            $xoopsTpl->assign('imagettfbbox', true);

            $form       = new \XoopsThemeForm(_AM_EXTGALLERY_WATERMARK_CONF, 'watermark_conf', 'watermark-border.php?op=conf', 'post', true);
            $fontSelect = new \XoopsFormSelect(_AM_EXTGALLERY_FONT, 'watermark_font', $helper->getConfig('watermark_font'));
            foreach ($fonts as $font) {
                $fontSelect->addOption($font, $font);
            }
            $form->addElement($fontSelect);

            $elementTray = new \XoopsFormElementTray(_AM_EXTGALLERY_WATERMARK_TEXT, '&nbsp;');

            $selected1 = 1 == $helper->getConfig('watermark_type') ? ' checked' : '';
            $disable   = 0 == $helper->getConfig('watermark_type') ? ' disabled="disabled"' : '';
            $style     = 0 == $helper->getConfig('watermark_type') ? ' style="background-color:#DDDDDD;"' : '';
            $onClick   = ' onClick="document.getElementById(\'watermark_text\').disabled = false; document.getElementById(\'watermark_text\').style.backgroundColor = \'#FFFFFF\';"';
            $WTextForm = '<input type="radio" name="watermark_type" value="1"' . $selected1 . $onClick . '> <input name="watermark_text" id="watermark_text" size="50" maxlength="255" value="' . $helper->getConfig('watermark_text') . '" type="text"' . $disable . $style . '><br>';

            $selected2 = 0 == $helper->getConfig('watermark_type') ? ' checked' : '';
            $onClick   = ' onClick="document.getElementById(\'watermark_text\').disabled = true; document.getElementById(\'watermark_text\').style.backgroundColor = \'#DDDDDD\';"';
            $WTextForm .= '<input type="radio" name="watermark_type" value="0"' . $selected2 . $onClick . '> ' . _AM_EXTGALLERY_PRINT_SUBMITTER_UNAME;

            $elementTray->addElement(new \XoopsFormLabel('', $WTextForm), false);
            $form->addElement($elementTray);
            $positionSelect = new \XoopsFormSelect(_AM_EXTGALLERY_POSITION, 'watermark_position', $helper->getConfig('watermark_position'));
            $positionSelect->addOption('tl', _AM_EXTGALLERY_TOP_LEFT);
            $positionSelect->addOption('tr', _AM_EXTGALLERY_TOP_RIGHT);
            $positionSelect->addOption('bl', _AM_EXTGALLERY_BOTTOM_LEFT);
            $positionSelect->addOption('br', _AM_EXTGALLERY_BOTTOM_RIGHT);
            $positionSelect->addOption('tc', _AM_EXTGALLERY_TOP_CENTER);
            $positionSelect->addOption('bc', _AM_EXTGALLERY_BOTTOM_CENTER);
            $positionSelect->addOption('lc', _AM_EXTGALLERY_LEFT_CENTER);
            $positionSelect->addOption('rc', _AM_EXTGALLERY_RIGHT_CENTER);
            $positionSelect->addOption('cc', _AM_EXTGALLERY_CENTER_CENTER);
            $form->addElement($positionSelect);
            $form->addElement(new \XoopsFormColorPicker(_AM_EXTGALLERY_WATERMARK_COLOR, 'watermark_color', $helper->getConfig('watermark_color')), false);
            $form->addElement(new \XoopsFormText(_AM_EXTGALLERY_WATERMARK_FONT_SIZE, 'watermark_fontsize', '2', '2', $helper->getConfig('watermark_fontsize')), false);
            $form->addElement(new \XoopsFormText(_AM_EXTGALLERY_WATERMARK_PADDING, 'watermark_padding', '2', '2', $helper->getConfig('watermark_padding')), false);
            $form->addElement(new \XoopsFormHidden('step', 'enreg'));
            $form->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

            $xoopsTpl->assign('watermarkform', $form->render());

            // Else display Warning message
        } else {
            $xoopsTpl->assign('freetypewarn', _AM_EXTGALLERY_WATERMARK_FREETYPE_WARN);
        }

        $form = new \XoopsThemeForm(_AM_EXTGALLERY_BORDER_CONF, 'border_conf', 'watermark-border.php?op=conf', 'post', true);
        $form->addElement(new \XoopsFormColorPicker(_AM_EXTGALLERY_INNER_BORDER_COLOR, 'inner_border_color', $helper->getConfig('inner_border_color')), false);
        $form->addElement(new \XoopsFormText(_AM_EXTGALLERY_INNER_BORDER_SIZE, 'inner_border_size', '2', '2', $helper->getConfig('inner_border_size')), false);
        $form->addElement(new \XoopsFormColorPicker(_AM_EXTGALLERY_OUTER_BORDER_COLOR, 'outer_border_color', $helper->getConfig('outer_border_color')), false);
        $form->addElement(new \XoopsFormText(_AM_EXTGALLERY_OUTER_BORDER_SIZE, 'outer_border_size', '2', '2', $helper->getConfig('outer_border_size')), false);
        $form->addElement(new \XoopsFormHidden('step', 'enreg'));
        $form->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        $xoopsTpl->assign('borderform', $form->render());

        $imageTest = getImageTest();

        if (isset($imageTest[0])) {
            $xoopsTpl->assign('imagetest', $imageTest[0]);
        }
        // Call template file
        $xoopsTpl->display(__DIR__ . '/../templates/admin/extgallery_admin_watermark_border.tpl');
        xoops_cp_footer();

        break;
}

/**
 * @return array
 */
function getImageTest()
{
    $ret = [];
    $rep = dirname(__DIR__) . '/assets/images/';
    $dir = opendir($rep);
    while (false !== ($f = readdir($dir))) {
        if (is_file($rep . $f)) {
            if (preg_match('/watermark-border-test/', $f)) {
                $ret[] = $f;
            }
        }
    }

    return $ret;
}

function deleteImageTest()
{
    $files = getImageTest();
    foreach ($files as $file) {
        unlink(__DIR__ . '/../assets/images/' . $file);
    }
}
