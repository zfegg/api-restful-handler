<?php


namespace Zfegg\ApiRestfulHandler\Resource;

use Laminas\Db\TableGateway\AbstractTableGateway;
use Zfegg\ApiRestfulHandler\Extension\ExtensionInterface;

class DbResource implements ResourceInterface
{
    use ResourceNotAllowedTrait;

    /**
     * @var ExtensionInterface[]
     */
    private array $extensions;

    /**
     * @var AbstractTableGateway|\Zfegg\Db\TableGateway\Feature\CommonCallFeature
     */
    private AbstractTableGateway $table;

    /**
     * DbResource constructor.
     * @param AbstractTableGateway $table
     * @param ExtensionInterface[] $extensions
     */
    public function __construct(AbstractTableGateway $table, array $extensions)
    {
        $this->table = $table;
        $this->extensions = $extensions;
    }

    public function getList(array $context = [])
    {
        $select = $this->table->getSql()->select();

        $result = null;

        foreach ($this->extensions as $extension) {
            if ($curResult = $extension->getList($select, $this->table, $context)) {
                $result = $curResult;
            }
        }

        return $result ?: $this->table->selectWith($select);
//            return $this->table->fetchPaginator(function (Select $select) use ($params) {
//                $this->queryParser->parseSelect($params, $select);
//            });
    }

    public function delete($id, array $context = []): void
    {
        $this->table->deletePrimary($id);
    }

    public function create($data, array $context = [])
    {
        $this->table->insert($data);

        return $this->table->find($this->table->getLastInsertValue());
    }

    public function update($id, $data, array $context = [])
    {
        $primary = $this->table->getPrimary()[0];
        $this->table->update($data, [$primary => $id]);

        return $this->table->find($id);
    }

    public function patch($id, $data, array $context = [])
    {
        return $this->update($id, $data, $context);
    }

    public function get($id, array $context = [])
    {
        return $this->table->find($id);
    }
}
