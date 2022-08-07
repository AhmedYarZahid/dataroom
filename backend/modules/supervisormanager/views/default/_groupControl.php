<!--<div class="margin">-->
<div class="btn-group groupControl" data-group-name="<?php echo $groupName ?>">
    <!--<button type="button" class="btn btn-default">
        Group Options
    </button>-->
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
        Group Options&nbsp;
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li>
            <a href="#" data-action="startProcessGroup">
                <i class="fa fa-play"></i>
                Start all
            </a>
        </li>

        <li class="divider"></li>
        <li>
            <a href="#" data-action="stopProcessGroup">
                <i class="fa fa-stop"></i>
                Stop all
            </a>
        </li>

        <!-- The rest action disabled for now. To enable need to remove part of the data-* name "DISABLED" -->
        <li class="divider"></li>
        <li>
            <a href="#" class="processConfigControl" data-actionDISABLED="addNewGroupProcess" style="color: #d3d3d3;">
                <i class="fa fa-plus"></i>
                Create new process
            </a>
        </li>

        <li class="divider"></li>
        <li>
            <a href="#" class="processConfigControl" data-group-process-deleteDISABLED style="color: #d3d3d3;">
                <i class="fa fa-remove"></i>
                Remove process
            </a>
        </li>

        <li class="divider"></li>
        <li>
            <a href="#" class="processConfigControl" data-actionDISABLED="deleteProcess" data-need-confirm style="color: #d3d3d3;">
                <i class="fa fa-minus-square"></i>
                Remove group
            </a>
        </li>
    </ul>
</div>
