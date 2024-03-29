<?php

namespace App\Http\Controllers;

use App\Models\Node;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use Inertia\Inertia;

class NodeController extends Controller
{
    //

    public function index() : \Inertia\Response
    {
        $nodes = Node::all();
        return Inertia::render('Nodes', [
            'canLogin' => true,
            'canRegister' => true,
            'laravelVersion' => '10',
            'phpVersion' => PHP_VERSION,
            'nodes' => $nodes
        ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addNewNode(Request $request) : \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|unique:nodes|max:255',
            'parent_node_id' => 'sometimes',
            'type' => 'required',
            'extra' => 'sometimes',
            'height' => 'required'
        ]);
        //Should have validation on parent node rules from endpoint 3

        Node::create([
            'name' => $validated['name'],
            'parent_node_id' => $validated['parent_node_id'] ?? null,
            'type' => $validated['type'],
            'extra' => $validated['extra'],
            'height' => $validated['height']
        ]);
        return response()->json('created node');
    }

    /**
     * @param Request $request
     * @param $nodeId
     * @return Response
     */
    public function getAllChildNodesFromNode(Request $request) : Response
    {
        // This would probadaly be better to do as a scope on model
        $parent = Node::find($request->query('node_id'));
        $children = Node::where('parent_node_id',$parent->id)->get();
        $data = [
            'parent' => $parent,
            'children' => $children
        ];

        return response($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeParentNodeOnNode(Request $request) : \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'node_id' => 'required',
            'new_parent_node_id' => 'required|nullable',
        ]);

        $node = Node::find($validated['node_id']);
        $newParent = Node::find($validated['new_parent_node_id']);
        if($node->type === 'corporation'){
            return \response()->json(
                [
                    'message' => 'Error: Node is already toplevel'
                ]
            );
        }
        if($node->type === 'building' && $newParent->type === 'corporation'){
            $node->parent_node_id = $newParent->id;
            return \response()->json([
                'message' => 'updated'
            ]);
        }
        if($node->tyoe === 'property' && $newParent->type === 'bulding'){
            $node->parent_node_id = $newParent->id;
            return \response()->json([
                'message' => 'updated'
            ]);
        }
        return response()->json('Error: Illegal new ParentId');
    }
}
