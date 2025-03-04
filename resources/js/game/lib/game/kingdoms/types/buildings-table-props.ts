import BuildingDetails from "../building-details";
import BuildingInQueueDetails from "../building-in-queue-details";

export default interface BuildingsTableProps {

    buildings: BuildingDetails[] | [];

    buildings_in_queue: BuildingInQueueDetails[]|[];

    dark_tables: boolean;

    view_building: (building?: BuildingDetails) => void;
}
