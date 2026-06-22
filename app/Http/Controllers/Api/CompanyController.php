<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\YandexParserService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    protected YandexParserService $parser;

    public function __construct(YandexParserService $parser)
    {
        $this->parser = $parser;
    }

    public function index()
    {
        return response()->json(Company::withCount('reviews')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
        ]);

        try {
            $company = $this->parser->saveToDatabase($validated['url']);
            return response()->json($company, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show($id)
    {
        $company = Company::with(['reviews' => function ($q) {
            $q->orderBy('date', 'desc')->paginate(50);
        }])->findOrFail($id);

        return response()->json($company);
    }
}