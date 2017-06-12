<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle;


use Symfony\Component\HttpKernel\Bundle\Bundle;
use Pim\Bundle\EnrichBundle\DependencyInjection\Reference\ReferenceFactory;
use Pim\Bundle\ImportExportBundle\DependencyInjection\Compiler\RegisterJobNameVisibilityCheckerPass;
use Pim\Bundle\ImportExportBundle\DependencyInjection\Compiler\RegisterJobParametersFormsOptionsPass;
use Pim\Bundle\ImportExportBundle\DependencyInjection\Compiler\RegisterJobTemplatePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DndGoogleShoppingConnectorBundle
 *
 * @author                 Agence Dn'D <contact@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class DndGoogleShoppingConnectorBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new RegisterJobTemplatePass())
            ->addCompilerPass(new RegisterJobParametersFormsOptionsPass(new ReferenceFactory()))
            ->addCompilerPass(new RegisterJobNameVisibilityCheckerPass(
                ['pim_connector.job_name.csv_product_export', 'pim_connector.job_name.xlsx_product_export', 'dnd_google_shopping_connector.job_name.xml_google_shopping_product_export']
            ));
    }
}
