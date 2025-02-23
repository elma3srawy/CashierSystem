<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecentInvoices extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            "status" => $this->status,
            "date_of_receipt"=> $this->when($this->date_of_receipt , $this->date_of_receipt , $this->created_at),
            "return_date"=> $this->return_date,
            "name" => $this->name,
            "price" => $this->price,
            "payment" => $this->payment,
            "data" => $this->when(
            $this->size || $this->model || $this->color ,
            $this->section_title . '-> ' . $this->size ."-". $this->model .'-' . $this->color,
            $this->title . '-' . $this->data
            )
        ];
    }
}
