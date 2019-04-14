<?php

namespace App\Services\Adminland\Position;

use App\Services\BaseService;
use App\Models\Company\Position;
use App\Services\Adminland\Company\LogAction;

class UpdatePosition extends BaseService
{
    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id' => 'required|integer|exists:companies,id',
            'author_id' => 'required|integer|exists:users,id',
            'position_id' => 'required|integer|exists:positions,id',
            'title' => 'required|string|max:255',
        ];
    }

    /**
     * Update a position.
     *
     * @param array $data
     * @return Position
     */
    public function execute(array $data): Position
    {
        $this->validate($data);

        $author = $this->validatePermissions(
            $data['author_id'],
            $data['company_id'],
            config('homas.authorizations.hr')
        );

        $position = Position::where('company_id', $data['company_id'])
            ->findOrFail($data['position_id']);

        $oldPositionTitle = $position->title;

        $position->title = $data['title'];
        $position->save();

        (new LogAction)->execute([
            'company_id' => $data['company_id'],
            'action' => 'position_updated',
            'objects' => json_encode([
                'author_id' => $author->id,
                'author_name' => $author->name,
                'position_id' => $position->id,
                'position_title' => $position->title,
                'position_old_title' => $oldPositionTitle,
            ]),
        ]);

        $position->refresh();

        return $position;
    }
}