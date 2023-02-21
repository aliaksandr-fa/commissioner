<?php declare(strict_types=1);

namespace Commissioner\Transaction\Presenter;


use Commissioner\Transaction\TransactionCommissionCollection;

/**
 * Class ConsoleCommissionsPresenter
 *
 * @package Commissioner\Transaction\Presenter
 */
class ConsoleCommissionsPresenter
{
    public function present(TransactionCommissionCollection $commissions): void
    {
        foreach ($commissions as $commission)
        {
            echo "{$commission->ceil(2)}\n";
        }
    }
}
